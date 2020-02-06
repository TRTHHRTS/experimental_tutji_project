<?php
namespace App;

class WithdrawalStatusEnum
{
    const PROCESSING = 0;
    const EXECUTED   = 1;
    const CANCELED   = 2;

    public static function valueOf($w): string {
        switch ($w) {
            case WithdrawalStatusEnum::PROCESSING:
                return 'В обработке';
            case WithdrawalStatusEnum::EXECUTED:
                return 'Исполнено';
            case WithdrawalStatusEnum::CANCELED:
                return 'Отменено';
            default:
                throwException(new \Exception('Неизвестный статус вывода'));
        }
        return null;
    }

    public static function values() {
        return [
            WithdrawalStatusEnum::PROCESSING => WithdrawalStatusEnum::valueOf(WithdrawalStatusEnum::PROCESSING),
            WithdrawalStatusEnum::EXECUTED => WithdrawalStatusEnum::valueOf(WithdrawalStatusEnum::EXECUTED),
            WithdrawalStatusEnum::CANCELED => WithdrawalStatusEnum::valueOf(WithdrawalStatusEnum::CANCELED),
        ];
    }
}
