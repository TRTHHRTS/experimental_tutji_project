<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserSettings
 *
 * @property int $id
 * @property int $user_id
 * @property bool $notify_new_messages
 * @property bool $notify_new_lesson_reviews
 * @property bool $notify_new_reviews
 * @property bool $notify_scheduled_lessons
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereNotifyNewLessonReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereNotifyNewMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereNotifyNewReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereNotifyScheduledLessons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings query()
 */
class UserSettings extends Model
{

    public $timestamps = false;

    protected $table = 'user_settings';

    protected $fillable = [
        'notify_new_messages', 'notify_new_lesson_reviews', 'notify_scheduled_lessons'
    ];

    protected $casts = [
        'notify_new_messages' => 'boolean',
        'notify_new_lesson_reviews' => 'boolean',
        'notify_scheduled_lessons' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
