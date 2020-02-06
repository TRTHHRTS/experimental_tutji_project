<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Lesson
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $short_desc
 * @property string|null $description
 * @property string|null $country_code
 * @property int $state_code
 * @property int $city_code
 * @property string|null $city_name
 * @property string|null $address
 * @property int|null $aging_id
 * @property int|null $rule_id
 * @property int $status
 * @property bool $equipment_have_all
 * @property string|null $equipment_have_all_desc
 * @property bool $equipment_first_aid
 * @property bool $equipment_memo_security
 * @property bool $equipment_extinguisher
 * @property float|null $lat
 * @property float|null $lng
 * @property mixed $price
 * @property int $pupil_count
 * @property bool $is_unique
 * @property bool $is_favorite
 * @property int|null $moder_id_who_approved
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Aging|null $aging
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LessonImage[] $images
 * @property-read \App\Models\User $moderWhoApproved
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReservedLesson[] $reservedLessons
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReservedTime[] $reservedTimes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @property-read \App\Models\Rule $rules
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $usersToRecommend
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereAgingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEquipmentExtinguisher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEquipmentFirstAid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEquipmentHaveAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEquipmentHaveAllDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEquipmentMemoSecurity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereIsPriceForHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereIsUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereModerIdWhoApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson wherePupilCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereShortDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereStateCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson query()
 */
class Lesson extends Model
{
    protected $fillable = [
        'name', 'short_desc', 'description', 'aging_id', 'rule_id', 'status', 'equipment_have_all', 'address',
        'equipment_have_all_desc', 'equipment_first_aid', 'equipment_memo_security', 'equipment_extinguisher',
        'duration', 'price', 'is_unique', 'is_favorite', 'pupil_count',
        'lat', 'lng', 'country_code', 'state_code', 'city_code', 'city_name'
    ];

    protected $casts = [
        'equipment_have_all' => 'boolean',
        'equipment_first_aid' => 'boolean',
        'equipment_memo_security' => 'boolean',
        'equipment_extinguisher' => 'boolean',
        'is_unique' => 'boolean',
        'is_favorite' => 'boolean',
        'price' => 'number'
    ];

    protected $hidden = [
        'pivot'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function categories() {
        $locale = app()->getLocale();
        return $this
            ->belongsToMany('App\Models\Category', 'category_lesson', 'lesson_id', 'category_id')
            ->select(['categories.id', "categories.name_$locale as name", "categories.desc_$locale as desc"]);
    }

    public function usersToRecommend() {
        return $this->belongsToMany('App\Models\User', 'recommended_lessons_users', 'lesson_id', 'user_id');
    }

    public function aging() {
        $locale = app()->getLocale();
        return $this->belongsTo('App\Models\Aging')
            ->select(['aging.id', "aging.name_$locale as name", "aging.desc_$locale as desc"]);
    }

    public function rules() {
        return $this->hasOne('App\Models\Rule');
    }

    public function reviews() {
        return $this->hasMany('App\Models\Review');
    }

    public function moderWhoApproved() {
        return $this->hasOne('App\Models\User', 'moder_id_who_approved')->select(array('id'));
    }

    public function images() {
        return $this->hasMany('App\Models\LessonImage')->select(['lesson_id', 'url', 'name']);
    }

    public function reservedTimes() {
        return $this->hasMany('App\Models\ReservedTime');
    }

    public function reservedLessons() {
        return $this->hasMany('App\Models\ReservedLesson');
    }
}
