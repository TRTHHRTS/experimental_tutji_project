<?php
namespace App\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ReserveMail extends Mailable {
    use Queueable;

    private $lesson;
    private $lessonDate;
    private $lessonTime;
    private $teacher;
    private $phone;

    /**
     * ReserveMail constructor.
     * @param $lesson
     * @param $lessonDate
     * @param $lessonTime
     * @param $teacher
     * @param $phone
     */
    public function __construct($lesson, $lessonDate, $lessonTime, $teacher, $phone)
    {
        $this->lesson = $lesson;
        $this->lessonDate = $lessonDate;
        $this->lessonTime = $lessonTime;
        $this->teacher = $teacher;
        $this->phone = $phone;
    }

    public function build(){
        return $this->view('mail.reserve')
            ->subject("У вас скоро занятие")
            ->with([
                'lesson' => $this->lesson,
                'lessonDate' => $this->lessonDate,
                'lessonTime' => $this->lessonTime,
                'teacher' => $this->teacher,
                'phone' => $this->phone,
            ]);
    }
}