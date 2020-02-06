<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ModerOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user == null || !($user->rights['admin_rights'] || $user->rights['moder_rights'])) {
            return response(__('errors.onlyModers'), 500);
        }
        return $next($request);
    }
}