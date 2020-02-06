<?php
namespace App\Http\Controllers;

use App\Mailable\LessonReviewMail;
use App\Models\Lesson;
use App\Models\LessonImage;
use App\Models\ReservedLesson;
use App\Models\ReservedTime;
use App\Models\Review;
use App\Models\Rule;
use App\StatusEnum;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Geographer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use MenaraSolutions\Geographer\City;
use MenaraSolutions\Geographer\State;

class LessonController extends Controller
{
    // Длина строк
    private static $ADDRESS_LENGTH = 200;
    private static $LESSONNAME_LENGTH = 200;
    private static $CITYNAME_LENGTH = 50;
    private static $SHORTDESC_LENGTH = 200;
    private static $DESC_LENGtH = 2000;
    // Максимальный размер изображений для загрузки (2MB)
    public static $MAX_IMAGE_SIZE = 2*1024*1024;

    public function newLesson()
    {
        $attrs = [
            'status' => StatusEnum::CREATED,
            'name' => __('common.newLesson'),
            'equipment_have_all' => true,
            'country_code' => 'RU',
            'city_name' => null,
            'price' => null
        ];
        $lesson = new Lesson($attrs);
        $savedLes = Auth::user()->lessons()->save($lesson);
        $lesson->rules()->save(new Rule());
        return response($savedLes->id);
    }

    public function deleteLesson() {
        $id = $_POST['ID'];
        $lesson = Lesson::find($id);
        if (is_null($lesson)) {
            return response(__('errors.wrongLessonId'), 500);
        }
        if ($lesson->user->id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 500);
        }
        if ($lesson->status !== StatusEnum::CREATED && $lesson->status !== StatusEnum::FINISHED) {
            return response(__('errors.onlyNewAndFinishedLessons'), 500);
        }
        $lesson->status = StatusEnum::DELETED;
        $lesson->save();
        return response(['status' => 0]);
    }

    /**
     * Просмотр занятия
     * @param $lessonId - идентификатор занятия
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function viewLesson($lessonId)
    {
        return $this->getLessonResponse($lessonId, false);
    }

    /**
     * Редактирование занятия
     * @param $lessonId - идентификатор занятия
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function editLesson($lessonId) {
        return $this->getLessonResponse($lessonId, true);
    }

    public function copyLesson(Request $request) {
        if (!$request->has('lessonId')) {
            return response(__('errors.noLessonId'), 422);
        }
        $lesson = Lesson::find($request->get("lessonId"));
        if ($lesson->user_id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 422);
        }
        $lessonAttrs = [
            'user_id' => Auth::id(),
            'name' => $lesson->name,
            'short_desc' => $lesson->short_desc,
            'description' => $lesson->description,
            'status' => StatusEnum::CREATED,
            'country_code' => $lesson->country_code,
            'state_code' => $lesson->state_code,
            'city_code' => $lesson->city_code,
            'city_name' => $lesson->city_name,
            'address' => $lesson->address,
            'aging_id' => $lesson->aging_id,
            'equipment_have_all' => $lesson->equipment_have_all,
            'equipment_have_all_desc' => $lesson->equipment_have_all_desc,
            'equipment_first_aid' => $lesson->equipment_first_aid,
            'equipment_memo_security' => $lesson->equipment_memo_security,
            'equipment_extinguisher' => $lesson->equipment_extinguisher,
            'lat' => $lesson->lat,
            'lng' => $lesson->lng,
            'price' => $lesson->price,
            'pupil_count' => $lesson->pupil_count
        ];
        $newLesson = new Lesson($lessonAttrs);
        $ruleAttrs = [
            'animals' => $lesson->rules->animals,
            'allow_smoking' => $lesson->rules->allow_smoking,
            'confirm_email' => $lesson->rules->confirm_email,
            'confirm_phone' => $lesson->rules->confirm_phone,
            'profile_photo' => $lesson->rules->profile_photo,
        ];
        $newRule = new Rule($ruleAttrs);
        Auth::user()->lessons()->save($newLesson);
        $newLesson->rules()->save($newRule);
        $cats = $lesson->categories->all();
        foreach ($cats as $category) {
            $newLesson->categories()->attach($category->id);
        }
        return response(['status' => 0, 'id' => $newLesson->id]);
    }

    /**
     * Получить response с информацией о занятии
     * @param $lessonId - идентификатор занятия
     * @param $isEdit   - флаг того, редактирование идет или просмотр
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function getLessonResponse($lessonId, $isEdit) {
        try {
            $lesson = Lesson::with('images', 'rules', 'aging')->findOrFail($lessonId);
            $authID = Auth::id();
            if ($lesson->status !== StatusEnum::ACTIVE) {
                if ($authID === null || $authID !== $lesson->user_id) {
                    return response(__('errors.onlyActiveLessons'), 500);
                }
            }
            $cats = [];
            foreach ($lesson->categories as $category) {
                array_push($cats, $category['id']);
            }
            $lesson['category_ids'] = $cats;
            $lesson['username'] = $lesson->user->name;
            $lesson['user_avatar'] = $lesson->user->userDetails->photo_url;
            $lesson['your_lesson'] = $lesson->user->id === Auth::id();
            $resTimes = $lesson->reservedTimes;
            $reserved = [];
            foreach ($resTimes as $resTime) {
                if (!$isEdit && $resTime->closed == true) {
                    continue;
                }
                $resDate = Carbon::createFromFormat("Y-m-d H:i", $resTime->lesson_date . ' ' . $resTime->lesson_time);
                $newRes = [
                    'id' => $resTime->id,
                    'lesson_date' => $resDate->format('d.m.Y'),
                    'lesson_time' => $resTime->lesson_time,
                    'duration' => $resTime->duration,
                    'isGone' => $resDate->isPast(),
                    'users_to_reserve' => DB::table('reserved_lessons')
                        ->join('users', 'users.id', '=', 'reserved_lessons.user_id')
                        ->where('reserved_time_id', $resTime->id)
                        ->select('users.name', 'users.id as user_id', 'reserved_lessons.count')
                        ->get()
                ];
                // При просмотре не показываем прошедшие записи
                if (!$isEdit && $newRes['isGone']) {
                    continue;
                }
                $reserved[] = $newRes;
            }
            $lesson['reserved_time'] = $reserved;

            // результат запроса
            $responseData = [];

            // если на просмотр - форматируем данные
            if ($lesson->country_code != null) {
                $lesson['country_name'] = Geographer::findOneByCode($lesson->country_code)->toArray()['name'];
                $responseData['states'] = HomeController::getStatesArray($lesson->country_code);
                if ($lesson->state_code != 0) {
                    $responseData['cities'] = HomeController::getCitiesArray($lesson->state_code);
                    if (!$isEdit) {
                        $lesson['state_name'] = State::build($lesson->state_code)->setLocale(app()->getLocale())->toArray()['name'];
                    }
                }
                if (!$isEdit && $lesson->city_name == null && $lesson->city_code != 0) {
                    $lesson->city_name = City::build($lesson->city_code)->setLocale(app()->getLocale())->toArray()['name'];
                }
                $responseData['hasNoCityInList'] = !is_null($lesson->city_name);
            }
            $lesson->hasDate = $lesson->date_begin && $lesson->date_end;
            $lesson->hasTime = $lesson->time_h_begin && $lesson->time_m_begin &&
                $lesson->time_h_end && $lesson->time_m_end;
            if ($lesson->hasTime) {
                $lesson->time_begin = $lesson->time_h_begin . ':' . $lesson->time_m_begin;
                $lesson->time_end = $lesson->time_h_end . ':' . $lesson->time_m_end;
            }
            $status = $lesson->status;
            $lesson->status = ['id' => $status, 'name' => StatusEnum::valueOf($status)];
            $responseData['lesson'] = $lesson;
            $responseData['reviews'] = $lesson->reviews()->with('user')->get();
            return response($responseData);
        } catch (ModelNotFoundException $e) {
            return response(__('errors.lessonNotFound'), 500);
        }
    }

    /**
     * Зарезервировать занятие
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function reserveLesson() {
        $result = [
            "status" => 0
        ];
        $lessonId = $_POST['LESSON_ID'];
        $count = $_POST['COUNT'];
        $resTimeId = $_POST['RESERVED_TIME_ID'];
        $message = null;
        if (empty($count)) {
            $message = __('errors.noCount');
        } else if (empty($resTimeId)) {
            $message = __('errors.noReserveId');
        } else if (empty($lessonId)) {
            $message = __('errors.noLessonId');
        } else {
            $lesson = Lesson::find($lessonId);
            if($lesson->user_id == Auth::id()) {
                $message = __('errors.noReserveOwnLesson');
            } else if ($lesson->rules->confirm_email && !Auth::user()->email_verified) {
                $message = __('errors.confirmEmail');
            } else if ($lesson->rules->confirm_phone && !Auth::user()->is_phone_confirmed) {
                $message = __('errors.confirmPhone');
            } else if ($lesson->rules->profile_photo && is_null(Auth::user()->userDetails->photo_url)) {
                $message = __('errors.needProfilePhoto');
            } else {
                $alreadyReserved = ReservedLesson::where([
                        'lesson_id' => $lessonId,
                        'user_id' => Auth::id(),
                        'reserved_time_id' => $resTimeId,
                        'closed' => false])
                        ->count() > 0;
                if ($alreadyReserved) {
                    $message = __('errors.alreadyReserved');
                }
            }
        }
        if (empty($message)) {
            $reservedLesson = new ReservedLesson();
            $reservedLesson->lesson_id = $lessonId;
            $reservedLesson->reserved_time_id = $resTimeId;
            $reservedLesson->user_id = Auth::id();
            $reservedLesson->count = $count;
            $reservedLesson->lesson_name = $lesson->name;
            $reservedLesson->teacher_phone = User::find($lesson->user_id)->phone;
            $reservedLesson->save();
            $result["data"] = ReservedLesson::getReservedLessonsForAuthUser(true);

        } else {
            $result["message"] = $message;
            $result["status"] = 4;
        }
        return response($result);
    }

    public function getReservedLessons() {
        return response(ReservedLesson::getReservedLessonsForAuthUser(true));
    }

    public function saveLesson() {
        $data = $_POST['lesson'];
        $lesson = Lesson::find($data['id']);
        if (!isset($lesson)) {
            return response(__('errors.ierror'), 500);
        }
        // Ошибка: урок не пренадлежит пользователю, он не может его изменять
        if (isset($lesson) && $data['user_id'] != $lesson->user_id) {
            return response(__('errors.cantEdit'), 500);
        }
        try {
            $lesson->name = null;
            if (array_key_exists("name", $data)) {
                if (mb_strlen($data['name']) > LessonController::$LESSONNAME_LENGTH) {
                    return response(__('errors.nameLengthExceeded', ['count' => LessonController::$ADDRESS_LENGTH]), 500);
                }
                $lesson->name = $data['name'];
            }
            $lesson->short_desc = null;
            if (array_key_exists("short_desc", $data)) {
                if (mb_strlen($data['short_desc']) > LessonController::$SHORTDESC_LENGTH) {
                    return response(__('errors.shortDescLengthExceeded', ['count' => LessonController::$SHORTDESC_LENGTH]), 500);
                }
                $lesson->short_desc = $data['short_desc'];
            }
            $lesson->description = null;
            if (array_key_exists("description", $data)) {
                if (mb_strlen($data['description']) > LessonController::$DESC_LENGtH) {
                    return response(__('errors.descLengthExceeded', ['count' => LessonController::$DESC_LENGtH]), 500);
                }
                $lesson->description = $data['description'];
            }
            // логика заполнения адреса:
            // если код страны не задан - пропускаем заполнение адреса
            // заполняем код области, если есть
            // если установлен признак использования названия города - проверяем, есть ли название и устанавливаем его
            // если признак не установлен - проверяем, указан ли код города и устанавливаем его
            if (array_key_exists("country_code", $data)) {
                if (mb_strlen($data['country_code']) === 2) {
                    $lesson->country_code = $data['country_code'];
                    if (array_key_exists("state_code", $data)) {
                        $lesson->state_code = intval($data['state_code']);
                    }
                    if ($data['USE_CITY_NAME'] === 'true') {
                        $lesson->city_name = null;
                        if (array_key_exists("city_name", $data)) {
                            if (mb_strlen($data['city_name']) > LessonController::$CITYNAME_LENGTH) {
                                return response(__('errors.shortDescLengthExceeded', ['count' => LessonController::$CITYNAME_LENGTH]), 500);
                            }
                            $lesson->city_name = $data['city_name'];
                            $lesson->city_code = 0;
                        }
                    } else {
                        if (array_key_exists("city_code", $data)) {
                            $lesson->city_code = intval($data['city_code']);
                            $lesson->city_name = null;
                        }
                    }
                } else {
                    $lesson->country_code = null;
                }
            }
            if (array_key_exists("address", $data)) {
                if (mb_strlen($data['address']) > LessonController::$ADDRESS_LENGTH) {
                    return response(__('errors.addressLengthExceeded', ['count' => LessonController::$ADDRESS_LENGTH]), 500);
                }
                $lesson->address = $data['address'];
            }
            $lesson->aging_id = null;
            if (array_key_exists("aging", $data) && !empty($data["aging"])) {
                $agingObj = $data["aging"];
                if (array_key_exists("id", $agingObj)) {
                    $lesson->aging_id = intval($data['aging']['id']);
                }
            }
            if (array_key_exists("pupil_count", $data)) {
                $pupilCount = intval($data['pupil_count']);
                $lesson->pupil_count = $pupilCount <= 6 ? $pupilCount : 0;
            }
            $lesson->lat = array_key_exists("lat", $data) ? floatval($data['lat']) : null;
            $lesson->lng = array_key_exists("lng", $data) ? floatval($data['lng']) : null;

            if (array_key_exists('price', $data) && !empty($data['price'])) {
                $lesson->price = intval($data['price']);
            }

            if (array_key_exists("equipment_have_all", $data)) {
                $haveAll = $data['equipment_have_all'] === 'true';
                $lesson->equipment_have_all = $haveAll;
                if (!$haveAll) {
                    $lesson->equipment_have_all_desc = array_key_exists("equipment_have_all_desc", $data) ? $data['equipment_have_all_desc'] : null;
                } else {
                    $lesson->equipment_have_all_desc = null;
                }
            }
            $isSaved = $lesson->save();
            if ($isSaved) {
                $rules = $lesson->rules == null ? new Rule() : $lesson->rules;
                $rulesData = array_key_exists("rules", $data) ? $data['rules'] : null;
                if (!is_null($rulesData)) {
                    if (array_key_exists("animals", $rulesData)) {
                        $rules->animals = $rulesData['animals'] === 'true';
                    }
                    if (array_key_exists("allow_smoking", $rulesData)) {
                        $rules->allow_smoking = $rulesData['allow_smoking'] === 'true';
                    }
                    if (array_key_exists("profile_photo", $rulesData)) {
                        $rules->profile_photo = $rulesData['profile_photo'] === 'true';
                    }
                    $rules->added_info = array_key_exists("added_info", $rulesData) ? trim($rulesData['added_info']): '';
                    $lesson->rules()->save($rules);
                }
                // пересохраняем категории
                $lesson->categories()->detach();
                if (array_key_exists('category_ids', $data)) {
                    foreach ($data['category_ids'] as $category) {
                        $lesson->categories()->attach($category);
                    }
                }
                // пересохраняем даты занятий
                ReservedTime::where('lesson_id', $lesson->id)
                    ->each(function($res) {
                        $res->delete();
                    });
                if (!empty($data["reserved_time"])) {
                    foreach ($data['reserved_time'] as $reserved) {
                        $resTime = new ReservedTime();
                        $resTime->lesson_date = Carbon::createFromFormat('d.m.Y', $reserved['lesson_date'])->toDateTimeString();
                        $resTime->lesson_time = $reserved['lesson_time'];
                        $resTime->duration = $reserved['duration'];
                        $lesson->reservedTimes()->save($resTime);
                    }
                }
            }
        } catch (Exception $e) {
            // Какая-то ошибка при разработке
            return response($e->getMessage(), 500);
        }
        return response(['status' => 0, 'lessonId' => $lesson->id]);
    }

    /**
     * Опубликовать урок.
     * По сути перед публикацией нужно удостовериться, что все обязательные поля заполнены
     */
    public function publishLesson($lessonId) {
        $errors = [];
        $lesson = Lesson::findOrFail($lessonId);
        if ($lesson->user->id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 500);
        }
        if ($lesson->status !== StatusEnum::CREATED) {
            return response(__('errors.publishOnlyNewLesson'), 500);
        }
        if (count($lesson->categories()->get()) == 0) {
            array_push($errors, __('errors.needCategory'));
        }
        if (count($lesson->aging()->get()) == 0) {
            array_push($errors, __('errors.needAging'));
        }
        if (empty($lesson->description)) {
            array_push($errors, __('errors.needDesc'));
        }
        if (empty($lesson->short_desc)) {
            array_push($errors, __('errors.needShortDesc'));
        }
        if (is_null($lesson->country_code)) {
            array_push($errors, __('errors.needCountry'));
        }
        if ($lesson->city_code === 0 && empty($lesson->city_name)) {
            array_push($errors, __('errors.needCity'));
        }
        if (empty($lesson->address)) {
            array_push($errors, __('errors.needAddress'));
        }
        if (!$lesson->equipment_have_all && empty($lesson->equipment_have_all_desc)) {
            array_push($errors, __('errors.needEquipment'));
        }
        if (is_null($lesson->lat) || is_null($lesson->lng)) {
            array_push($errors, __('errors.needGeo'));
        }
        $minPrice = intval(env("APP_MINIMUM_LESSON_PRICE"));
        if (is_null($lesson->price)) {
            array_push($errors, __('errors.needPositivePrice'));
        } else if ($lesson->price < $minPrice) {
            array_push($errors, __('errors.lessonPriceNeedMore', ['price' => $minPrice]));
        }
        if ($lesson->pupil_count === 0) {
            array_push($errors, __('errors.needPositivePupils'));
        }
        if (count($lesson->images) === 0) {
            array_push($errors, __('errors.needImages'));
        }
        $times = $lesson->reservedTimes()->get();
        $uniqueTimes = array();
        if (count($times) <= 0) {
            array_push($errors, __('errors.needLessonDates'));
        }
        foreach ($times as $curTime) {
            $lessonDate = Carbon::createFromFormat("Y-m-d H:i", $curTime->lesson_date . ' ' . $curTime->lesson_time);
            if ($lessonDate->lte(\Carbon\Carbon::now())) {
                array_push($errors, __('errors.needActualDates'));
                break;
            }
            $dateStr = $lessonDate->format("dmYHi");
            if(in_array($dateStr, $uniqueTimes)) {
                array_push($errors, __('errors.sameTime', ['date' => $lessonDate->format('d.m.Y H:i')]));
                break;
            } else {
                array_push($uniqueTimes, $dateStr);
            }
        }
        if (count($errors) > 0) {
            $result = ['errors' => $errors];
        } else {
            $lesson->status = StatusEnum::ACTIVE;
            $lesson->save();
            $result = ['id' => $lesson->id];
        }
        return response($result, 200);
    }

    public function saveLessonReview(Request $request) {
        if (!$request->has('lessonId')) {
            return response(__('errors.noLessonId'), 422);
        }
        $review = new Review();
        $review->lesson_id = $request->get('lessonId');
        $review->message = $request->get('message');
        $review->rating = $request->get('rating');
        if (Auth::user()->reviews()->where(['lesson_id' => $review->lesson_id])->count() > 0) {
            return response(__('errors.hasReviewAlready'), 422);
        }
        if (Auth::user()->reviews()->save($review) === false) {
          return response(__('errors.ierror'), 422);
        }
        // Отправка уведомления при необходимости
        $teacher = $review->lesson->user;
        if (!is_null($teacher->email) && $teacher->email_verified && $teacher->settings->notify_new_lesson_reviews) {
            $mail = new LessonReviewMail(Auth::user()->name, $review->lesson->name, $review->rating, $review->message);
            Mail::to($teacher->email)->send($mail);
        }
        return response(['status' => 0]);
    }

    public function addImage(Request $request) {
        $lessonId = $_POST['lessonId'];
        if (!isset($_POST['lessonId'])) {
            return response(__('errors.noLessonId'), 422);
        }
        $lesson = Lesson::find($lessonId);
        if ($lesson->user->id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 422);
        }

        if ($lesson->images()->count() >= 10) {
            return response(__('errors.imageCountExceeded'), 422);
        }

        // Проверка существования директории и создание если необходимо
        $path = "images/lessons/$lessonId";
        $dir = "./$path";
        if(!is_dir($dir)) mkdir($dir, 0777, true);

        if ($request->hasFile('file')) {
            $image = $request->file('file');

            $imageType = exif_imagetype($image->getPathname());
            if ($imageType !== IMAGETYPE_JPEG && $imageType !== IMAGETYPE_PNG) {
                return response(__('errors.onlyJpegImages'), 422);
            }
            if ($image->getSize() > LessonController::$MAX_IMAGE_SIZE) {
                return response(__('errors.imagesLength', ['length' => LessonController::$MAX_IMAGE_SIZE / 1024 / 1024]), 422);
            }

            try {
                $this->validate($request, [
                    'file' => 'required|image|mimes:jpeg,png|max:2048',
                ]);
            } catch (Exception $error) {
                return response(__('errors.incorrectImage'), 422);
            }

            $name = $lesson->id . "_" . time() . "_" . $lesson->images()->count() . "." . $image->getClientOriginalExtension();

            $uploadedFile = $image->move($dir, $name);

            $lessonImage = new LessonImage();
            $lessonImage->name = $uploadedFile->getFilename();
            $lessonImage->url = "$path/$name";
            $lesson->images()->save($lessonImage);
        }
        return response($lesson->images);
    }

    public function deleteImage() {
        $lessonId = $_POST['lesson_id'];
        if (!isset($_POST['lesson_id'])) {
            return response(__('errors.noLessonId'), 422);
        }
        $lesson = Lesson::find($lessonId);
        if ($lesson->user->id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 422);
        }
        $url = $_POST['url'];
        try {
            $fileDeleted = File::delete("./$url");
            $deleted = LessonImage::where([['lesson_id', $lessonId], ['url', $url]])->delete();
            if (!$deleted) {
                return response(__('errors.deleteError'), 422);
            }
        } catch (Exception $e) {
            return response(__('errors.ierror'), 422);
        }
        return $lesson->images;
    }

    public function recommendLesson() {
        if (!isset($_POST['ID'])) {
            return response(__('errors.noLessonId'), 500);
        }
        $lessonId = ($_POST['ID']);
        if ($lessonId == 0) {
            return response(__('errors.wrongLessonId'), 500);
        }
        $hasRecommend = Auth::user()->recommendedLessons()->where('lesson_id', $lessonId)->get()->count() > 0;
        if ($hasRecommend) {
            $result = ['result' => 1];
        } else {
            Auth::user()->recommendedLessons()->attach($lessonId);
            $result = ['result' => 0, 'recommendations' => Auth::user()->recommendedLessons()->get(['lessons.id'])];
        }
        return response($result);
    }

    public function notRecommendLesson() {
        if (!isset($_POST['ID'])) {
            return response(__('errors.noLessonId'), 500);
        }
        $lessonId = ($_POST['ID']);
        if ($lessonId == 0) {
            return response(__('errors.wrongLessonId'), 500);
        }
        $notRecommend = Auth::user()->recommendedLessons()->where('lesson_id', $lessonId)->get()->count() == 0;
        if ($notRecommend) {
            $result = ['result' => 1];
        } else {
            Auth::user()->recommendedLessons()->detach($lessonId);
            $result = ['result' => 0, 'recommendations' => Auth::user()->recommendedLessons()->get(['lessons.id'])];
        }
        return response($result);
    }
}
