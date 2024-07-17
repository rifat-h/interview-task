<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Ui\FlashMessageGenerator;
use Symfony\Component\HttpFoundation\Response;

class UserEnabledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->status) {
            Auth::logout();
            FlashMessageGenerator::generate('danger', "your account is disabled");
            return redirect()->route('login');
        }
        return $next($request);
    }
}
