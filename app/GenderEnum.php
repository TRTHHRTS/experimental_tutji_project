<?php
namespace App;

class GenderEnum
{
    const MALE    = 1;
    const FEMALE  = 2;
    const UNKNOWN = 0;

    public static function valueOf($gender)
    {
        switch ($gender) {
            case GenderEnum::MALE:
                return __('common.genderMale');
            case GenderEnum::FEMALE:
                return __('common.genderFemale');
            case GenderEnum::UNKNOWN:
                return __('common.genderUnset');
            default:
                throwException(new \Exception(__('errors.ierror')));
        }
    }
}
