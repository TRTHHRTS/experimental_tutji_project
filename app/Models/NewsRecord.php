<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\NewsRecord
 *
 * @property int $id
 * @property int $user_id
 * @property string $title_ru
 * @property string $title_en
 * @property string $message_ru
 * @property string $message_en
 * @property int $published
 * @property int $importance
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereImportance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereMessageEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereMessageRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord whereTitleRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsRecord query()
 */
class NewsRecord extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'user_id', 'title_ru', 'title_en', 'message_ru', 'message_en', 'importance', 'published'
    ];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function getTitle() {
        $locale = app()->getLocale();
        return $this["title_$locale"];
    }

    public function getContent() {
        $locale = app()->getLocale();
        return $this["message_$locale"];
    }


    public static function getLatestNews($count = 3) {
        $locale = app()->getLocale();
        return DB::table('news')->select(['id', "title_$locale as title", "message_$locale as message", 'importance'])->where('published', true)->latest()->take(3)->get();
    }

}
