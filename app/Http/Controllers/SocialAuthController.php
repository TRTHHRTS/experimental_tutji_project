<?php
namespace App\Http\Controllers;

use App\Models\SocialData;
use App\SocialNetworksEnum;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserRole;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Auth;
use Socialite;

class SocialAuthController extends Controller
{
    /**
     * @param $provider - название соц. сети
     * @return mixed переход на аутентификацию в соц. сети
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Обработка ответа от соц. сети. Регистрация нового пользователя или вход.
     * @param $providerName - название соц. сети, с помощью которой осущевляется вход
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function callback($providerName)
    {
        $socialKey = SocialNetworksEnum::valueOf($providerName);
        $userInfo = Socialite::driver($providerName)->stateless()->user();
        $socialEmail = $userInfo->getEmail();
        // если есть email в ответе от соц. сети - ищем юзера в бд по email
        // иначе создаем нового юзера без email
        if (!is_null($socialEmail )) {
            $existedUser = User::whereEmail($socialEmail)->first();
            // если в бд есть юзер с таким email - обновляем его данные
            // иначе создаем нового юзера с таким email
            if (!is_null($existedUser)) {
                if($existedUser['email_verified']) {
                    $existedSocial = $existedUser->socialAccounts;
                    // если у юзера есть информация о соц. сетях - обновляем ее
                    // иначе - создаем новую инфу о соц. сети
                    if (!is_null($existedSocial)) {
                        $existedUser->socialAccounts()->update(array(
                            $socialKey => $userInfo->getId(),
                            'last_login_via' => $providerName
                        ));
                    } else {
                        $socialData = $this->createSocialData($userInfo, $socialKey, $providerName);
                        $existedUser->socialAccounts()->save($socialData);
                    }
                    Auth::login($existedUser);
                } else {
                    Auth::login($this->createNewUser(null, $userInfo, $socialKey, $providerName));
                }
            } else {
                Auth::login($this->createNewUser($socialEmail, $userInfo, $socialKey, $providerName));
            }
        } else {
            $socialData = SocialData::where($socialKey, $userInfo->getId())->first();
            // если пользователь ни разу не заходил через эту соц. сеть - создаем новую учетную запись
            // иначе возвращаем нужную учетку
            if (is_null($socialData)) {
                $user = $this->createNewUser(null, $userInfo, $socialKey, $providerName);
            } else {
                $user = User::findOrFail($socialData->id_user);
            }
            Auth::login($user);
        }
        return redirect('/#/');
    }

    /**
     * @param $email        - email юзера
     * @param $userInfo     - информация от юзера из ответа соц. сети
     * @param $socialKey    - ключ идентификатора соц. сети в бд
     * @param $providerName - название соц. сети
     * @return User сущность пользователя
     */
    private function createNewUser($email, $userInfo, $socialKey, $providerName) {
        $user = new User();
        if (!is_null($email)) {
            $user->email = $email;
            $user->email_verified = true;
        }
        $user->name = $userInfo->getName();
        $isSaved = $user->save();
        if ($isSaved) {
            $userDetails = new UserDetails();
            //$userDetails['gender'] = SocialNetworksEnum::FACEBOOK === $providerName ? strtoupper($userInfo['gender']) : 0;
            $userDetails->photo_url = $userInfo->getAvatar();
            $user->userDetails()->save($userDetails);
            $user->socialAccounts()->save($this->createSocialData($userInfo, $socialKey, $providerName));
            $user->rights()->save(new UserRole());
            $user->settings()->save(new UserSettings());
        }
        return $user;
    }

    /**
     * @param $userInfo   - информация о пользователе из соц. сети
     * @param $socialKey  - ключ соц. сети в бд
     * @param $providerName - название соц. сети
     * @return SocialData информация о соц. сетях
     */
    private function createSocialData($userInfo, $socialKey, $providerName) {
        $socialData = new SocialData();
        $socialData[$socialKey] = $userInfo->getId();
        $socialData->last_login_via = $providerName;
        return $socialData;
    }
}
