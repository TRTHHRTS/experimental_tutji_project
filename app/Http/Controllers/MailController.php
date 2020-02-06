<?php
namespace App\Http\Controllers;

use App\MailNews;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{

    public function sendNews() {
        if (Auth::check()) {
            Mail::to(Auth::user()->email)->send(new MailNews('Поздравлямба', 'Тут подробненько новости'));
        }
    }
}