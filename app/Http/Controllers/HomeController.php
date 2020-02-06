<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthenticatesUsers;
use App\Models\Lesson;
use App\Models\NewsRecord;
use App\StatusEnum;
use App\Models\User;
use App\ReserveStatusEnum;
use App\Models\Aging;
use App\Models\Category;
use App\WithdrawalStatusEnum;
use Geographer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MenaraSolutions\Geographer\City;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\State;

class HomeController extends Controller
{

    public function initApp() {
        $properties = array(
            'has_sms' => env("APP_HAS_SMS", false),
            'has_email' => env("APP_HAS_EMAIL", false),
            'has_news' => env("APP_HAS_NEWS", true),
            'default_lang' => env("APP_DEFAULT_LANG", 'en')
        );
        $countries[] = Country::build('RU')->useShortNames()->setLocale(app()->getLocale())->toArray();
        $countries[] = Country::build('US')->useShortNames()->setLocale(app()->getLocale())->toArray();

        $result = [
            'user' => AuthenticatesUsers::getFullUser(),
            'countries' => $countries,
            'states' => Geographer::findOneByCode('RU')->getStates()->sortBy('name')->toArray(),
            'statuses' => StatusEnum::getStatuses(),
            'categories' => Category::getCategories(),
            'agings' => Aging::getAging(),
            'systemProperties' => $properties,
            'reserveStatuses' => ReserveStatusEnum::values(),
            'withdrawalStatuses' => WithdrawalStatusEnum::values()
        ];
        if ($properties['has_news']) {
            $result['news'] = NewsRecord::getLatestNews();
        }
        return response($result);
    }

    public function getStates($code) {
        return response(self::getStatesArray($code));
    }

    public function getCities($stateCode) {
        return response(self::getCitiesArray($stateCode));
    }

    public static function getStatesArray($code) {
        return Geographer::findOneByCode($code)->getStates()->sortBy('name')->toArray();
    }

    public static function getCitiesArray($stateCode) {
        return State::build($stateCode)->getCities()->setLocale(app()->getLocale())->sortBy('name')->toArray();
    }

    public function getUserInfo($id) {
        Carbon::setToStringFormat('d.m.Y');
        $user = User::find($id);
        if ($user == null) {
            return response(__('errors.wrongUserId'), 500);
        }
        $result['name'] = $user->name;
        $result['email_verified'] = $user->email_verified;
        $result['is_phone_confirmed'] = $user->is_phone_confirmed;
        $result['created_at'] = $user->created_at->toDateTimeString();
        $details = $user->userDetails;
        $result['gender'] = $details->gender;
        $result['photo_url'] = $details->photo_url;
        $result['birthday'] = $details->birthday;
        $result['lesson_count'] = $user->lessons()->where('status', '=', StatusEnum::ACTIVE)->get()->count();
        $result['recommendations'] = $user->recommendedLessons()->get(['lessons.id', 'lessons.name']);
        return response($result);
    }

    public function findLessons() {
        $data = $_GET;
        $cityName = !empty($data['cityName']) ? $data['cityName'] : null;
        $dateComparsion = '=';
        if (!empty($data['plannedDate'])) {
            $plannedDate = $data['plannedDate'];
        } else {
            $plannedDate = Carbon::today();
            $dateComparsion = '>=';
        }
        $lessonName = !empty($data['lessonName']) ? $data['lessonName'] : null;
        $cats = isset($data['CATS']) ? $data['CATS'] : [];
        if(array_key_exists("resultsOnPage", $data)) {
            $limit = $data['resultsOnPage'];
        } else {
            // TODO
            $limit = 10;
        }
        $lessonIds = DB::table('lessons')->select('lessons.id')
            ->join('category_lesson', function ($join) use ($cats) {
                $join->on('lessons.id', '=', 'category_lesson.lesson_id')
                    ->whereIn('category_id', $cats);
            })
            ->where('status', StatusEnum::ACTIVE)
            ->when(!empty($cityName), function ($query) use ($cityName) {
                return $query->where('city_name', 'like', "%" . $cityName . "%");
            })
            ->when(!empty($lessonName), function ($query) use ($lessonName) {
                return $query->where('name', 'like', "%" . $lessonName . "%");
            })
            ->when(!empty($plannedDate), function ($query) use ($plannedDate, $dateComparsion) {
                $query->join('lesson_reserved_times', function ($join) use ($plannedDate, $dateComparsion) {
                    $join->on('lessons.id', '=', 'lesson_reserved_times.lesson_id')
                        ->whereDate('lesson_date', $dateComparsion, $plannedDate);
                });
            })
            ->distinct()->skip($data['offset'])->take($limit)->get()->toArray();
        $lessons = array();
        foreach ($lessonIds as $lessonId) {
            $lesson = Lesson::with([
                'user' => function($query) {$query->select('id', 'name');},
                'user.userDetails' => function($query) {$query->select('user_id', 'birthday', 'photo_url', 'gender');},
                'images',
                'categories', 'aging',
                'reservedTimes',
                'reviews' => function($query) {$query->select('lesson_id', 'rating');},
                'reservedLessons' => function($query) {$query->select('reserved_time_id', 'lesson_id', 'count');}
                ])->find($lessonId->id)
                ->toArray();
            $lessons[] = $lesson;
        }
        return response(['lessons' => $lessons]);
    }

    public function findCities() {
        $q = $_POST['QUERY'];
        if (empty($q)) {
            return response(['cities' => [['value' => 'Москва']]]);
        }
        $codes = DB::table('lessons')
            ->distinct()
            ->select(['city_code'])
            ->where('city_code', '>', 0)
            ->get()->toArray();
        $cityNames = array_map(array($this, 'getCityCode'), $codes);
        $filtered = array_values(array_filter(
            $cityNames,
            function ($val, $key) use ($q) {
                return stripos($val['value'], $q) !== false;
            },
            ARRAY_FILTER_USE_BOTH
        ));
        return response(['cities' => $filtered]);
    }
    function getCityCode($info){
        return ['value' => City::build($info->city_code)->setLocale(app()->getLocale())->getName()];

    }
}
