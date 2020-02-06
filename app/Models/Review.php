<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int $user_id
 * @property int $lesson_id
 * @property int $rating
 * @property string|null $message
 * @property int $readed
 * @property int $published
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Lesson $lesson
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereReaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review query()
 */
class Review extends Model
{
    protected $table = 'lesson_reviews';

    protected $fillable = [
    ];

    protected $casts = [
    ];

    /**
     *
     * @param $lessonId - идентификатор урока
     * @return array|null
     */
    public static function getLessonRating($lessonId) {
        $reviews = Review::where('lesson_id', $lessonId)->get();
        if (count($reviews) > 0) {
            $rating = [
                'count' => count($reviews),
            ];
            $ratingSum = 0;
            foreach ($reviews as $review) {
                $ratingSum += $review->rating;
            }
            $rating['intValue'] = round($ratingSum / $rating['count']);
            $rating['accurateValue'] = bcdiv($ratingSum, $rating['count'], 2);
            return $rating;
        }
        return null;
    }

    public function user() {
        return $this->belongsTo('App\Models\User')->select(array('id', 'name'));
    }

    public function lesson() {
        return $this->belongsTo('App\Models\Lesson');
    }
}
