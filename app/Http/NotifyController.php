<?php
namespace App\Http;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotifyController
{

    public static function hasReserveNotify(int $type, int $userId, $param): int {
        return DB::table('notify_data')->where(['type' => $type, 'param' => $param, 'user_id' => $userId])->count();
    }

    public static function addNotifyRecord(int $userId, int $type, $param): bool {
        return DB::table('notify_data')->insert([
            'type' => $type,
            'user_id' => $userId,
            'param' => $param,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}