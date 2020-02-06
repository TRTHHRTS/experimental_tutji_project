<?php
namespace App\Models;

use App\ReserveStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\ReservedLesson
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $user_id
 * @property int $reserved_time_id
 * @property int $count
 * @property string $lesson_name
 * @property string $teacher_phone
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Lesson $lesson
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereLessonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereReservedTimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereTeacherPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereUserId($value)
 * @mixin \Eloquent
 * @property int $reserve_status
 * @property string|null $reason
 * @property bool $closed
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereReserveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereReserveStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson whereReason($value)
 * @property-read \App\Models\ReservedTime $reserveTime
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReservedLesson query()
 */
class ReservedLesson extends Model
{
    protected $table = 'reserved_lessons';

    protected $casts = [
        'closed' => 'boolean'
    ];

    public function lesson() {
        return $this->belongsTo('App\Models\Lesson', 'lesson_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function reserveTime() {
        return $this->belongsTo('App\Models\ReservedTime', 'reserved_time_id');
    }

    public static function getReservedLessonsForAuthUser($isNew) {
        return DB::table('reserved_lessons as rl')
            ->leftJoin('lesson_reserved_times as res_times', 'res_times.id', '=', 'rl.reserved_time_id')
            ->leftJoin('lessons', 'lessons.id', '=', 'rl.lesson_id')
            ->leftJoin('lesson_reviews as rel_revs', function ($join) {
                $join
                    ->on('rel_revs.user_id', '=', 'rl.user_id')
                    ->on('rel_revs.lesson_id', '=', 'rl.lesson_id');
            })
            ->where('rl.user_id', Auth::id())
            ->where('rl.reserve_status', $isNew ? '=' : '!=', ReserveStatusEnum::NEW_RESERVE)
            ->select('rl.lesson_id as id', 'rl.count', 'rl.teacher_phone as phone', 'rl.lesson_name as name', 'rl.reserve_status',
                'res_times.lesson_time as ltime', 'res_times.lesson_date as ldate', 'res_times.duration',
                'rel_revs.id AS rid', 'rel_revs.message', 'rel_revs.rating',
                'rl.user_id', 'rl.id as rlid', 'lessons.user_id as teacher_id', 'lessons.address')
            ->get();
    }

    public static function getCommonLessonReserves($params) {
        // Шта тут происходит? Работает? Не трогать.
        $query = DB::table('reserved_lessons as rl');
        $query->leftJoin('lessons', 'lessons.id', '=', 'rl.lesson_id');
        if ($params->asTeacher) {
            $query->leftJoin('users', 'users.id', '=', 'rl.user_id');
            $query->where('lessons.user_id', Auth::id());
        } else {
            $query->leftJoin('users', 'users.id', '=', 'lessons.user_id');
            $query->where('rl.user_id', Auth::id());
        }
        $query->leftJoin('lesson_reviews as reviews', function($join)
        {
            $join->on('reviews.user_id', '=', 'rl.user_id');
            $join->on('reviews.lesson_id', '=', 'lessons.id');

        });
        $query->leftJoin('lesson_reserved_times as res_times', 'res_times.id', '=', 'rl.reserved_time_id');
        $query->where('rl.reserve_status', $params->isNew ? '=' : '!=', ReserveStatusEnum::NEW_RESERVE);
        if (!$params->showClosed) {
            $query = $query->where('rl.closed', false);
        }
        $query->select('lessons.id as lesson_id', 'lessons.name',
            'rl.count', 'rl.reserve_status', 'rl.user_id', 'rl.id as rlid', 'rl.closed', 'rl.reason',
            'res_times.lesson_time as ltime', 'res_times.lesson_date as ldate', 'res_times.duration',
            'users.name as user_name', 'users.id as user_id', 'reviews.message', 'reviews.rating', 'reviews.id as review_id');
        if ($params->needPagination) {
            $query->take($params->pageSize)->offset(($params->curPage - 1) * $params->pageSize);
        }
        $query->orderBy('rl.created_at');
        return ['data' => $query->get(), 'total' => $query->count()];
    }
}
