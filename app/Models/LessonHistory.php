<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LessonHistory
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $user_id
 * @property int $price
 * @property string $lesson_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereLessonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonHistory query()
 */
class LessonHistory extends Model
{
    protected $table = 'lesson_history';

    protected $fillable = [
    ];

    protected $hidden = [
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
