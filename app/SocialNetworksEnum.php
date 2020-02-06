<?php
namespace App;

use Laravel\Socialite\Two\InvalidStateException;

/**
 * Список соц. сетей, через которые можно войти в приложение
 * Class SocialNetworksEnum
 * @package Models
 */
class SocialNetworksEnum
{
    const FACEBOOK = 'facebook';
    const GOOGLE   = 'google';
    const VK       = 'vkontakte';

    public static function valueOf($social)
    {
        switch ($social) {
            case SocialNetworksEnum::FACEBOOK:
                return 'id_fb';
            case SocialNetworksEnum::GOOGLE:
                return 'id_google';
            case SocialNetworksEnum::VK:
                return 'id_vk';
            default:
                throw new InvalidStateException("Недопустимый тип соц. сети");
        }
    }
}
