<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Faq
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereQuestion($value)
 * @mixin \Eloquent
 * @property string $question_ru
 * @property string $question_en
 * @property string $answer_ru
 * @property string $answer_en
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereAnswerEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereAnswerRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereQuestionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq whereQuestionRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Faq query()
 */
class Faq extends Model
{
    protected $table = 'faq';

    public $timestamps = false;

    protected $fillable = [
        'question_ru', 'question_en', 'answer_ru', 'answer_en'
    ];

    public static function getFaqs() {
        $locale = app()->getLocale();
        return DB::table('faq')->select(['id', "question_$locale as question", "answer_$locale as answer"])->get();
    }
}
