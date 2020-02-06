<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LessonMessages
 *
 * @property-read \App\Models\User $admin
 * @property-read \App\Models\ReservedLesson $reserveRecord
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property-read \App\Models\ReservedLesson $reserve
 * @property int $id
 * @property int $user_id
 * @property int $reserve_id
 * @property string $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereReserveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonMessages query()
 */
class LessonMessages extends Model
{
    protected $table = 'lesson_messages';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function isAdministration(): bool {
        return $this->user->rights->moder_rights || $this->user->rights->admin_rights;
    }

    public function reserve() {
        return $this->belongsTo('App\Models\ReservedLesson', 'reserve_id');
    }

}