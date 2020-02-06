<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LessonImage
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $url
 * @property string|null $name
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage whereUrl($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonImage query()
 */
class LessonImage extends Model
{
    protected $table = 'lesson_images';

    protected $fillable = [
    ];

    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson');
    }
}
