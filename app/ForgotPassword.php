<?php
namespace App;

use Illuminate\Bus\Queueable;
use \Illuminate\Mail\Mailable;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11.06.2017
 * Time: 15:58
 */
class ForgotPassword extends Mailable {
    use Queueable;

    protected $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.forgot_password')
            ->with([
                'token' => $this->token
            ]);
    }
}