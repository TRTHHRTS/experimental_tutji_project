<?php
namespace App;

class StatusEnum
{
    const CREATED  = 0;
    const ACTIVE   = 1;
    const DELETED  = 2;
    const FINISHED = 3;

    public static function valueOf($status)
    {
        switch ($status) {
            case StatusEnum::CREATED:
                return __('common.statusNew');
            case StatusEnum::ACTIVE:
                return __('common.statusActive');
            case StatusEnum::DELETED:
                return __('common.statusDeleted');
            case StatusEnum::FINISHED:
                return __('common.statusFinished');
            default:
                return __('common.statusNo');
        }
    }

    public static function getStatuses() {
        return [
            StatusEnum::CREATED => __('common.statusNew'),
            StatusEnum::ACTIVE => __('common.statusActive'),
            StatusEnum::DELETED => __('common.statusDeleted'),
            StatusEnum::FINISHED => __('common.statusFinished')
        ];
    }
}