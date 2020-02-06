<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public static $USERS_COUNT = 2;

    public static $LESSONS_COUNT = 30;

    public static $NORMAL_DATE_FORMAT = 'Y-m-d H:i:s';

    public function run()
    {
        $this->call(CategoriesSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(LessonsSeeder::class);
        $this->call(ReviewsSeeder::class);
        $this->call(MessagesSeeder::class);
    }

    public static function randomDateToNow($sStartDate) {
        $fMin = strtotime($sStartDate);
        $fMax = Carbon::now()->getTimestamp();
        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);
        // Convert back to the specified date format
        return date(DatabaseSeeder::$NORMAL_DATE_FORMAT, $fVal);
    }

    public static function randomDate($sStartDate, $sEndDate, $sFormat = 'Y-m-d H:i:s')
    {
        // Convert the supplied date to timestamp
        $fMin = strtotime($sStartDate);
        $fMax = strtotime($sEndDate);
        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);
        // Convert back to the specified date format
        return date($sFormat, $fVal);
    }

    public static function randomDate2($beginDate, $endDate, $sFormat = 'Y-m-d H:i:s') {
        return date($sFormat, rand($beginDate->timestamp, $endDate->timestamp));
    }

    public static function randomLessonTime() {
        $h = rand(0, 23);
        $minutesRange = ['00','15','30','45'];
        return ($h < 10 ? '0'.$h : $h) . ':' . $minutesRange[rand(0, count($minutesRange)-1)];
    }
}
