<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\sms\SmsAccount;
use App\sms\SmsAddressBook;
use App\sms\SmsApi;
use App\sms\SmsExceptions;
use App\sms\SmsStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    private static $SENDER_NAME = 'TUTJI.RU';
    private static $EXPIRED_TIME = 60;
    private static $Gateway = NULL;
    private static $Addressbook = NULL;
    private static $Exceptions = NULL;
    private static $Account = NULL;
    private static $Stat = NULL;

    public function __construct() {
        SmsController::$Gateway = new SmsApi(env('SMS_PRIVATE_KEY'), env('SMS_PUBLIC_KEY'), env('SMS_URL_GATEWAY'));
        SmsController::$Addressbook = new SmsAddressBook(SmsController::$Gateway);
        SmsController::$Exceptions = new SmsExceptions(SmsController::$Gateway);
        SmsController::$Account = new SmsAccount(SmsController::$Gateway);
        SmsController::$Stat = new SmsStat(SmsController::$Gateway);
    }

    public function sendSmsCode() {
        $phone = Auth::user()->phone;
        if  (!isset($phone)) {
            return response(__('sms.noPhone'), 500);
        }
        $phone = '7' . $phone;
        if (Auth::user()->is_phone_confirmed) {
            return response(__('sms.alreadyConfirmed'), 500);
        }
        // ищем активный код для данного пользователя
        $dbRecord = DB::table('sms_codes')->where(["user_id" => Auth::id(), 'active' => true])->first();
        // генерируем код подтверждения
        $code = $this->makeDigitCode();
        $messageText = 'код подтверждения tutji: ' . $code;

        $dateNow = Carbon::now();
        // код еще ни разу не высылали, высылаем
        if (is_null($dbRecord)) {
            return $this->sendCode($phone, $messageText, $code, $dateNow);
        }
        // когда-то уже высылали код
        $dateDbRecord = Carbon::parse($dbRecord->updated_at);
        $hasExpired = $dateNow->diffInMinutes($dateDbRecord) > SmsController::$EXPIRED_TIME;
        // старый код просрочен, можно высылать новый
        if ($hasExpired) {
            return $this->sendCode($phone, $messageText, $code, $dateNow);
        }
        // старый код не просрочен, его можно вводить
        $dateAfterTenMinutes = $dateDbRecord->addMinutes(SmsController::$EXPIRED_TIME);
        return response([
            'status' => 4,
            'codeSent'=> true,
            'message' => __('sms.activeCode') .
                $dateNow->diffForHumans($dateAfterTenMinutes, true, false)
        ]);
    }

    private function sendCode($phone, $message, $code, $dateNow) {
        $res = SmsController::$Stat->sendSMS(SmsController::$SENDER_NAME, $message, $phone, 0 , 0);
        if (isset($res["result"]["error"])) {
            return response(['status' => 4, 'message' => "Ошибка: " . $res["result"]["error"]]);
        }
        // все существующие коды делаем неактивными
        DB::table('sms_codes')->where(['user_id' => Auth::id(), 'active' => true])->update(['active' => false]);
        DB::table('sms_codes')->insert([
            'user_id' => Auth::id(),
            'phone' => $phone,
            'message' => $message,
            'code' => $code,
            'sms_id' =>$res["result"]["id"],
            'price' => $res["result"]["price"],
            'currency' => $res["result"]["currency"],
            'created_at' => $dateNow,
            'updated_at' => $dateNow
        ]);
        return response(['status' => 0]);
    }

    public function getBalance() {
        return response(SmsController::$Account->getUserBalance());
    }

    public function verifyCode() {
        if (Auth::user()->is_phone_confirmed) {
            return response(__('sms.alreadyConfirmed'), 500);
        }
        $code = $_POST['CODE'];
        if  (!isset($code) || intval($code) === 0) {
            return response(__('sms.noCode'), 500);
        }
        $dbRecord = DB::table('sms_codes')->where(["user_id" => Auth::id(), 'active' => true])->first();
        if (is_null($dbRecord)) {
            return response(['status' => 4, 'message' => __('sms.noCodeSend')]);
        }
        $dateNow = Carbon::now();
        $dateDbRecord = Carbon::parse($dbRecord->updated_at);
        $hasExpired = $dateNow->diffInMinutes($dateDbRecord) > SmsController::$EXPIRED_TIME;
        if ($hasExpired) {
            return response(['status' => 4, 'message' => __('sms.codeExpires')]);
        }
        $phone = Auth::user()->phone;
        if  (!isset($phone)) {
            return response(['status' => 4, 'message' => __('sms.noPhone')]);
        }
        $phone = '7' . $phone;
        if (intval($code) !== $dbRecord->code || $phone !== $dbRecord->phone) {
            return response(['status' => 4, 'message' => __('sms.codeNotMatch')]);
        }
        $id = Auth::id();
        $user = User::find($id);
        $user->is_phone_confirmed = true;
        $user->save();
        DB::table('sms_codes')->where('id', $dbRecord->id)->update(['confirmed' => true, 'active' => false]);
        return response(['status' => 0, 'message' => __('sms.phoneConfirmed', ['phone' => $phone])]);
    }

    private function makeDigitCode() {
        return rand(100000, 999999);
    }
}
?>