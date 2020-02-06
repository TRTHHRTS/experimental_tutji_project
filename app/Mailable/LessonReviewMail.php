<?php
namespace App\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class LessonReviewMail extends Mailable {
    use Queueable;

    private $user;
    private $lesson;
    private $rating;
    private $review;

    public function __construct($user, $lesson, $rating, $review) {
        $this->user = $user;
        $this->lesson = $lesson;
        $this->rating = $rating;
        $this->review = $review;
    }

    public function build(){
        return $this->view('mail.lesson_review')
            ->subject("Новый отзыв о вашем занятии")
            ->with([
                'user' => $this->user,
                'lesson' => $this->lesson,
                'rating' => $this->rating,
                'review' => $this->review,
            ]);
    }
}