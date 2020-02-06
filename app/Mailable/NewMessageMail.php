<?php
namespace App\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class NewMessageMail extends Mailable {
    use Queueable;

    private $user;
    private $sender;
    private $date;
    private $message;

    public function __construct($user, $sender, $date, $message)
    {
        $this->user = $user;
        $this->sender = $sender;
        $this->date = $date;
        $this->message = $message;
    }

    public function build(){
        return $this->view('mail.new_message')
            ->subject("Вас пришло новое сообщение")
            ->with([
                'username' => $this->user,
                'sender' => $this->sender,
                'date' => $this->date,
                'mes' => $this->message,
            ]);
    }
}