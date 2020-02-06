<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthenticatesUsers;
use App\Mailable\EmailVerification;
use App\Models\ReservedLesson;
use App\Models\User;
use App\Models\Withdrawal;
use App\StatusEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function setEmail() {
        $data = $_POST;
        if (!array_key_exists("email", $data) || empty($data['email']) || empty($data['email']['value'])) {
            return response(__("errors.incorrectRequest"), 422);
        }
        if (!empty(Auth::user()->email)) {
            return response(__("errors.emailAlreadySetted"), 422);
        }
        $email = $data['email']['value'];
        if (User::whereEmail($email)->count() > 0) {
            return response(__("errors.emailSetToAnotherAcoount"), 422);
        }
        Auth::user()->email = $email;
        Auth::user()->save();
        return response(AuthenticatesUsers::getFullUser());
    }

    public function sendEmail() {
        if (!empty(Auth::user()->email)) {
            Auth::user()->email_token = str_random(20);
            Auth::user()->save();
            $email = new EmailVerification(Auth::user());
            Mail::to(Auth::user()->email)->send($email);
            return response(0);
        }
        return response(__("errors.incorrectRequest"), 422);
    }

    public function saveProfileData() {
        if (Auth::check()) {
            $data = $_POST;
            $user = Auth::user();
            if (array_key_exists("name", $data) && !empty($data['name'])) {
                $user->name =  $data['name'];
            }
            if (array_key_exists("gender", $data)) {
                $user->userDetails->gender = intval($data['gender']);
            }
            if (array_key_exists("birthday", $data) && !empty($data['birthday'])) {
                $user->userDetails->birthday =  $data['birthday'];
            }
            if (array_key_exists("phone", $data) && !empty($data['phone'])) {
                $user->phone =  $data['phone'];
            }
            $user->save();
            $user->userDetails->save();
            return AuthenticatesUsers::getFullUser();
        }
        return response(__('errors.noAuth'), 401);
    }

    public function changeNotify() {
        if (Auth::check()) {
            $data = $_POST;
            $user = Auth::user();
            $settings = $user->settings;
            if (array_key_exists("notifyId", $data) && $settings[$data['notifyId']] !== null) {
                $settings[$data['notifyId']] = !$settings[$data['notifyId']] ;
            }
            $settings->save();
            return 0;
        }
        return response(__('errors.noAuth'), 401);
    }

    public function changePassword() {
        if (Auth::check()) {
            $data = $_POST;
            $user = Auth::user();
            if (array_key_exists("old", $data)) {
                $oldPassword = $data['old'];
            }
            $notifyId = $data['notifyId'];
            if (array_key_exists("notifyId", $data) && $user->settings[$notifyId] !== null) {
                $user->settings()[$notifyId] = !$user->settings[$notifyId] ;
            }
            $user->settings->save();
            return 0;
        }
        return response(__('errors.noAuth'), 401);
    }

    public function saveNewAvatar(Request $request) {
        $path = "images/profiles";
        $dir = "./$path";
        if(!is_dir($dir)) mkdir($dir, 0777, true);

        if ($request->hasFile('AVATAR')) {
            $image = $request->file('AVATAR');

            $imageType = exif_imagetype($image->getPathname());
            if ($imageType !== IMAGETYPE_JPEG && $imageType !== IMAGETYPE_PNG) {
                return response(__('errors.onlyJpegImages'), 422);
            }
            if ($image->getSize() > LessonController::$MAX_IMAGE_SIZE) {
                return response(__('errors.imagesLength', ['length' => LessonController::$MAX_IMAGE_SIZE / 1024 / 1024]), 422);
            }
            try {
                $this->validate($request, [
                    'AVATAR' => 'required|image|mimes:jpeg,png|max:2048',
                ]);
            } catch (Exception $error) {
                return response(__('errors.incorrectImage'), 422);
            }

            $name = Auth::id() . "_" . time() . '.' . $image->getClientOriginalExtension();

            $existingPhotoUrl = "./" . Auth::user()->userDetails->photo_url;
            if (!is_null($existingPhotoUrl) && file_exists($existingPhotoUrl)) {
                if (!File::delete($existingPhotoUrl)) {
                    Log::error(__('errors.cantDeleteFile', ['name' => $existingPhotoUrl]));
                }
            }
            $uploadedFile = $image->move($dir, $name);
            Auth::user()->userDetails()->update(['photo_url' => "$path/$name"]);
            return response("$path/$name");
        }
        return null;
    }

    public function getLessonReserves() {
        $params = $this->getLessonReservesParams($_GET);
        return response(ReservedLesson::getCommonLessonReserves($params));
    }

    private function getLessonReservesParams($data)
    {
        $isPagination = isset($data['currentPage']) && isset($data['pageSize']);
        $params = (object) ['needPagination' => $isPagination];
        if ($isPagination) {
            $params->curPage = intval($data['currentPage']);
            $params->pageSize = intval($data['pageSize']);
        }
        $params->showClosed = isset($data['showClosed']) ? $data['showClosed'] === "true" : false;
        $params->asTeacher = isset($data['asTeacher']) ? $data['asTeacher'] === "true" : false;
        $params->isNew = isset($data['isNew']) ? $data['isNew'] === "true" : false;
        return $params;
    }

    public function getLessons() {
        $lessons = Auth::user()->lessons()
            ->select(['lessons.id', 'lessons.name', 'lessons.created_at', 'lessons.short_desc', 'lessons.status', 'lessons.aging_id'])
            ->with('aging')
            ->whereIn('lessons.status', [StatusEnum::ACTIVE, StatusEnum::CREATED, StatusEnum::FINISHED])
            ->orderByDesc('lessons.created_at')
            ->get()->toArray();
        return response($lessons);
    }
}
