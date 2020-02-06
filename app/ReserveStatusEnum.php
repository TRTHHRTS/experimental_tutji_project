<?php

namespace App;

/**
 * Class ReserveStatusEnum статусы резервирования занятия
 * @package Models
 */
class ReserveStatusEnum
{
    const NEW_RESERVE             =  0;
    const COMPLETED               = 10;
    const TEACHER_NOT_COME        = 21;
    const PUPIL_CANCEL_RECORD     = 30;
    const TEACHER_CANCEL_RECORD   = 31;
    const TEACHER_CANCEL_LESSON   = 32;
    const OTHER_REASONS           = 40;
    const AWAITING_ADMINISTRATION = 41;
    const AWAITING_TEACHER        = 42;
    const AWAITING_PUPIL          = 43;

    public static function valueOf($reserve): string {
        switch ($reserve) {
            case ReserveStatusEnum::NEW_RESERVE:
                return __('common.rse_NEW_RESERVE');
            case ReserveStatusEnum::COMPLETED:
                return __('common.rse_COMPLETED');
            case ReserveStatusEnum::TEACHER_NOT_COME:
                return __('common.rse_TEACHER_NOT_COME');
            case ReserveStatusEnum::PUPIL_CANCEL_RECORD:
                return __('common.rse_PUPIL_CANCEL_RECORD');
            case ReserveStatusEnum::TEACHER_CANCEL_RECORD:
                return __('common.rse_TEACHER_CANCEL_RECORD');
            case ReserveStatusEnum::TEACHER_CANCEL_LESSON:
                return __('common.rse_TEACHER_CANCEL_LESSON');
            case ReserveStatusEnum::OTHER_REASONS:
                return __('common.rse_OTHER_REASONS');
            case ReserveStatusEnum::AWAITING_ADMINISTRATION:
                return __('common.rse_AWAITING_ADMINISTRATION');
            case ReserveStatusEnum::AWAITING_TEACHER:
                return __('common.rse_AWAITING_TEACHER');
            case ReserveStatusEnum::AWAITING_PUPIL:
                return __('common.rse_AWAITING_PUPIL');
            default:
                throwException(new \Exception(__('common.rse_Unknown')));
        }
        return null;
    }

    public static function values() {
        return [
            ReserveStatusEnum::NEW_RESERVE => ReserveStatusEnum::valueOf(ReserveStatusEnum::NEW_RESERVE),
            ReserveStatusEnum::COMPLETED => ReserveStatusEnum::valueOf(ReserveStatusEnum::COMPLETED),
            ReserveStatusEnum::TEACHER_NOT_COME => ReserveStatusEnum::valueOf(ReserveStatusEnum::TEACHER_NOT_COME),
            ReserveStatusEnum::PUPIL_CANCEL_RECORD => ReserveStatusEnum::valueOf(ReserveStatusEnum::PUPIL_CANCEL_RECORD),
            ReserveStatusEnum::TEACHER_CANCEL_RECORD => ReserveStatusEnum::valueOf(ReserveStatusEnum::TEACHER_CANCEL_RECORD),
            ReserveStatusEnum::TEACHER_CANCEL_LESSON => ReserveStatusEnum::valueOf(ReserveStatusEnum::TEACHER_CANCEL_LESSON),
            ReserveStatusEnum::OTHER_REASONS => ReserveStatusEnum::valueOf(ReserveStatusEnum::OTHER_REASONS),
            ReserveStatusEnum::AWAITING_ADMINISTRATION => ReserveStatusEnum::valueOf(ReserveStatusEnum::AWAITING_ADMINISTRATION),
            ReserveStatusEnum::AWAITING_TEACHER => ReserveStatusEnum::valueOf(ReserveStatusEnum::AWAITING_TEACHER),
            ReserveStatusEnum::AWAITING_PUPIL => ReserveStatusEnum::valueOf(ReserveStatusEnum::AWAITING_PUPIL)
        ];
    }
}
