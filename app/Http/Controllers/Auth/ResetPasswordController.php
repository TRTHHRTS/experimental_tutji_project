<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @return токен сброса пароля
     */
    public function getResetToken() {
        return $this->broker()->createToken(\Auth::user());
    }

    /**
     * Display the password reset view for the given token.
     * If no token is present, display the link request form.
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     */
    public function getResetInfo(Request $request, $token)
    {
        return ['token' => $token, 'email' => $request->email];
    }


    /**
     * Reset the given user's password.
     * @param  \Illuminate\Http\Request  $request
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        $result = new Response();
        $result->code = $response == Password::PASSWORD_RESET ? 0 : 4;
        $result->data = trans($response);
        return $result;
    }
}
