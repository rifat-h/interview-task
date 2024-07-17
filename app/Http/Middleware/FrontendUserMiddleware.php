<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Ui\FlashMessageGenerator;
use Symfony\Component\HttpFoundation\Response;

class FrontendUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->hasRole('Frontend')) {
            Auth::logout();
            FlashMessageGenerator::generate('danger', 'You are not allowed to access this page');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
