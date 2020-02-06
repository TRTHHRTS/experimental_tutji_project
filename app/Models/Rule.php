<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rule
 *
 * @property bool $animals
 * @property bool $allow_smoking
 * @property bool $confirm_email
 * @property bool $confirm_phone
 * @property bool $profile_photo
 * @property string|null $added_info
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereAddedInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereIsForAnimals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereIsSmokingAllow($value)
 * @mixin \Eloquent
 * @property int $lesson_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereAllowSmoking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereAnimals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereConfirmEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereConfirmPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rule query()
 */
class Rule extends Model
{
    protected $table = 'lesson_rules';

    public $timestamps = false;

    protected $primaryKey = 'lesson_id';

    protected $fillable = [
        'animals', 'allow_smoking', 'confirm_email', 'confirm_phone', 'profile_photo', 'added_info'
    ];

    protected $casts = [
        'animals' => 'boolean',
        'allow_smoking' => 'boolean',
        'confirm_email' => 'boolean',
        'confirm_phone' => 'boolean',
        'profile_photo' => 'boolean',
    ];

    public function lesson() {
        return $this->belongsTo('App\Models\Lesson');
    }
}
