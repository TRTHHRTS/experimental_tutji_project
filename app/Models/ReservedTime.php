<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReservedTime
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $lesson_date
 * @property string $lesson_time
 * @property string $duration
 * @property boolean $closed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Lesson $lesson
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReservedLesson[] $reserves
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereLessonDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereLessonTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedTime query()
 */
class ReservedTime extends Model
{
    protected $table = 'lesson_reserved_times';

    protected $fillable = [
        'lesson_date', 'lesson_time', 'duration', 'isGone', 'closed'
    ];

    protected $casts = [
        'closed' => 'boolean'
    ];

    protected $visible = [
        'id', 'lesson_date', 'lesson_time', 'duration', 'isGone', 'closed'
    ];

    public function lesson() {
        return $this->belongsTo('App\Models\Lesson');
    }

    public function reserves() {
        return $this->hasMany('App\Models\ReservedLesson', 'reserved_time_id');
    }

    public function getCarbonResTime(): Carbon {
        return Carbon::parse($this->lesson_date . ' ' . $this->lesson_time . ':00');
    }

}
