<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user == null || !$user->rights['admin_rights']) {
            return response(__('errors.onlyAdmins'), 500);
        }
        return $next($request);
    }
}