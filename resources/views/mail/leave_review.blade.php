<!-- Тема письма: Напоминание: оставьте свой отзыв о преподавателе -->
<h2>{{__('mail.hello', ['name' => $username])}}</h2>
<h3>{{__('mail.needReviewTitle', ['lesson' => $lesson, 'teacher' => $teacher])}}</h3>
<h4>{{__('mail.needReviewText', ['teacher' => $teacher])}}</h4>
<h5>{{__('mail.needReviewTip', ['teacher' => $teacher])}}</h5>
