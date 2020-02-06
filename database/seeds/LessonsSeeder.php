<?php

use App\ReserveStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\State;

class LessonsSeeder extends Seeder
{
    public function run()
    {
        $descs = ['Описание_1'];
        $titles = ['Заголовок'];
        $hours = ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24'];
        $minutes = ['00','05','10','15','20','25','30','35','40','45','50','55'];
        $countries = ['RU', 'US'];

        $statuses = array_keys(ReserveStatusEnum::values());

        $beginDate = Carbon::now();
        $endDate = Carbon::now()->addMonth(3);

        for ($i = 1; $i <= DatabaseSeeder::$LESSONS_COUNT; $i++) {
            $tempBool = rand(1, 10) > 5;
            $countryCode = $countries[rand(1, 10) > 2 ? 0 : 1];
            $states = Country::build($countryCode)->getStates()->setLocale(app()->getLocale())->toArray();
            $stateCode = $states[rand(0, count($states)-1)]['code'];
            $cities = State::build($stateCode)->getCities()->setLocale(app()->getLocale())->toArray();
            $needCityCode = rand(1, 10) > 3;
            $cityCode = 0;
            if ($needCityCode && !is_null($cities) && count($cities) > 0) {
                $cityCode = $cities[rand(0, count($cities)-1)]['code'];
            }
            $cityName = $cityCode == 0 ? 'Калуга-' . rand(0, 10000) : null;
            $lessons[] = [
                'user_id' => rand(1, DatabaseSeeder::$USERS_COUNT),
                'status' => rand(0,1),
                'name' => $titles[rand(0,9)],
                'short_desc' => 'Пример краткого описания занятия ' . $i . ', максимуму 200 символов.',
                'description' => $descs[rand(0,9)],
                'country_code' => $countryCode,
                'state_code' => $stateCode,
                'city_code' => $cityCode,
                'city_name' => $cityName,
                'aging_id' => rand(1, 5),
                'equipment_have_all' => true,
                'equipment_first_aid' => rand(1, 10) > 5 ? true : false,
                'equipment_memo_security' => rand(1, 10) > 5 ? true : false,
                'equipment_extinguisher' => rand(1, 10) > 5 ? true : false,
                'lat' => $tempBool ? rand(50, 60) : null,
                'lng' => $tempBool ? rand(40, 70) : null,
                'price' => rand(100, 800),
                'pupil_count' => rand(1, 6),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ];
            $rules[] = [
                'lesson_id' => $i,
                'animals' => rand(0,1),
                'allow_smoking' => rand(0,1),
                'confirm_email' => true,
                'confirm_phone' => true,
                'profile_photo' => true,
                'added_info' => '',

            ];
            for ($j = 1; $j <= 8; $j++) {
                $tempBool = rand(1, 10) > 5;
                if ($tempBool) {
                    $categories[] = [
                        'category_id' => $j,
                        'lesson_id' => $i
                    ];
                }
            }
            for ($j = 1; $j <= 5; $j++) {
                $tempBool = rand(1, 10) > 5;
                if ($tempBool) {
                    $reservedTimes[] = [
                        'lesson_id' => $i,
                        'lesson_date' => DatabaseSeeder::randomDate2($beginDate, $endDate),
                        'lesson_time' => DatabaseSeeder::randomLessonTime(),
                        'duration' => '0'.rand(0,9).rand(0,5).rand(0,9),
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ];
                }
            }

        }
        if (!empty($lessons)) {
            DB::table('lessons')->insert($lessons);
        }
        if (!empty($rules)) {
            DB::table('lesson_rules')->insert($rules);
        }
        if (!empty($categories)) {
            DB::table('category_lesson')->insert($categories);
        }
        if (!empty($reservedTimes)) {
            DB::table('lesson_reserved_times')->insert($reservedTimes);
        }
    }
}
