<?php
namespace App\Models;

use App\ForgotPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'email_token'
    ];

    protected $casts = [
        'is_phone_confirmed' => 'boolean',
        'email_verified' => 'boolean',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_token', 'pivot'
    ];

    /**
     * Отправка уведомления об изменении пароля.
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();
        Mail::to($email)->send(new ForgotPassword(env('APP_URL') . '/#/reset/' . $token));
    }

    public function userDetails() {
        return $this->hasOne('App\Models\UserDetails');
    }

    public function lessons() {
        return $this->hasMany('App\Models\Lesson');
    }

    public function recommendedLessons() {
        return $this->belongsToMany('App\Models\Lesson', 'recommended_lessons_users', 'user_id', 'lesson_id');
    }

    public function inMessages() {
        return $this->hasMany('App\Models\Message', 'rcpt_id');
    }

    public function outMessages() {
        return $this->hasMany('App\Models\Message', 'sender_id');
    }

    public function reviews() {
        return $this->hasMany('App\Models\Review');
    }

    public function lessonHistory() {
        return $this->hasMany('App\Models\LessonHistory');
    }

    public function rights() {
        return $this->hasOne('App\Models\UserRole', 'user_id');
    }

    public function settings() {
        return $this->hasOne('App\Models\UserSettings');
    }

    public function socialAccounts() {
        return $this->hasOne('App\Models\SocialData', 'id_user');
    }

    public function reservedLessons() {
        return $this->hasMany('App\Models\ReservedLesson');
    }

    public function setVerified(): bool {
        $this->email_verified = 1;
        $this->email_token = null;
        return $this->save();
    }

    public function isOnline(): bool {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function hasAnyRights(): bool {
        return $this->rights->moder_rights || $this->rights->admin_rights;
    }
}
