<?php
namespace App\Http\Middleware;

use Closure;
use Cookie;
use Geographer;
use Illuminate\Support\Carbon;
use MenaraSolutions\Geographer\City;
use MenaraSolutions\Geographer\Contracts\ManagerInterface;
use MenaraSolutions\Geographer\State;

class LanguageMiddleware {

    public function handle($request, Closure $next)
    {
        $lang = $request->cookie('lang');
        if (is_null($lang)) {
            $lang = config('app.locale');
        }
        app()->setLocale($lang);
        Carbon::setLocale($lang);
        Geographer::setLocale($lang);
        $response = $next($request);
        if (is_null($request->cookie('lang'))) {
            $response->withCookie(Cookie::make('lang', $lang, 100800, null, null, null, false));
        }
        return $response;
    }
}