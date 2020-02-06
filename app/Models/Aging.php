<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Aging
 *
 * @property int $id
 * @property string $name
 * @property string|null $desc
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereName($value)
 * @mixin \Eloquent
 * @property string $name_ru
 * @property string $name_en
 * @property string|null $desc_ru
 * @property string|null $desc_en
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereDescEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereDescRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging whereNameRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Aging query()
 */
class Aging extends Model
{
    protected $table = 'aging';

    public $timestamps = false;

    public static function getAging() {
        $locale = app()->getLocale();
        return DB::table('aging')->select(['id', "name_$locale as name", "desc_$locale as desc"])->get();
    }
}
