<?php
namespace App\Mailable;

use Illuminate\Bus\Queueable;
use \Illuminate\Mail\Mailable;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11.06.2017
 * Time: 15:58
 */
class MailNews extends Mailable {
    use Queueable;

    protected $title;

    protected $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $content) {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.news')
            ->with([
                'title' => $this->title,
                'content' => $this->content,
            ]);
    }
}