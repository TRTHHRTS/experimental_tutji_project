<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class FaqController extends Controller
{

    public function getFaq() {
        return response(Faq::getFaqs());
    }

    public function addFaq() {
        $data = $_POST;
        $record = new Faq();
        $record->question = $data['q'];
        $record->answer = $data['a'];
        $record->save();
        return response(['status' => 0, 'faq' => $record]);
    }

    public function deleteFaq() {
        $id = $_POST['ID'];
        if (!isset($id)) {
            return response(__('errors.incorrectRequest'), 500);
        }
        $item = Faq::find($id);
        $item->delete();

        return response(['status' => 0]);
    }
}
