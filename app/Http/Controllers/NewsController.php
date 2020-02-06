<?php
namespace App\Http\Controllers;

use App\Mailable\MailNews;
use App\Models\NewsRecord;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function getLatestNews() {
        $news = NewsRecord::getLatestNews(10);
        return response($news);
    }

    public function addNewsRecord() {
        $data = $_POST;
        if (!isset($data['title_ru']) || !isset($data['title_en']) || !isset($data['content_ru']) || !isset($data['content_en'])) {
            return response(500, __('errors.incorrectRequest'));
        }
        $record = new NewsRecord();
        $record->user_id = Auth::id();
        $record->title_ru = $data['title_ru'];
        $record->title_en = $data['title_en'];
        $record->message_ru = $data['content_ru'];
        $record->message_en = $data['content_en'];
        $record->importance = intval($data['importance']);
        $record->save();
        $news = NewsRecord::getLatestNews();

//        $mail = new MailNews($record->getTitle(), $record->getContent());
//        $users = User::where('email', '<>', null)->get();
//        Mail::to($users)->send($mail);

        return response(['status' => 0, 'news' => $news]);
    }

    public function removeNewsRecord() {
        $record = NewsRecord::findOrFail($_POST['ID']);
        $record->published = false;
        $record->update();
        $news = NewsRecord::getLatestNews();
        return response(['status' => 0, 'news' => $news]);
    }

}
