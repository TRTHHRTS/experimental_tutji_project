<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function getFeedback() {
        $feeds = DB::table('feedback')->orderBy('created_at')->get();
        return response($feeds);
    }

    public function addFeedback() {
        $data = $_POST;
        $content = $data['content'];
        if (strlen($content) > 2000) {
            return response(__('errors.contentLength'), 500);
        }
        $userEmail = $data['userEmail'];
        if (strlen($userEmail) > 100) {
            return response(__('errors.emailLength'), 500);
        }
        DB::table('feedback')->insert(['content' => $content, 'user_email' => $userEmail]);
        return response(['status' => 0]);
    }

    public function answerFeedback() {
        $id = $_POST['ID'];
        if (!isset($id)) {
            return response(__('errors.incorrectRequest'), 500);
        }
        $answer = $_POST['answer'];
        if (!isset($answer) || strlen($answer) > 2000) {
            return response(__('errors.answerError'), 500);
        }
        $result = DB::table('feedback')->where('id', $id)->update(['answer' => $answer]);
        return response(['result' => $result]);
    }
}
