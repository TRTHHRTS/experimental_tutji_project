<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $desc
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @mixin \Eloquent
 * @property string $name_ru
 * @property string $name_en
 * @property string|null $desc_ru
 * @property string|null $desc_en
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereDescEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereDescRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereNameRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 */
class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = false;

    public function lessons() {
        return $this->belongsToMany('App\Models\Lesson', 'category_lesson', 'category_id', 'lesson_id');
    }

    public static function getCategories() {
        $locale = app()->getLocale();
        return DB::table('categories')->select(['id', "name_$locale as name", "desc_$locale as desc"])->get();
    }
}
