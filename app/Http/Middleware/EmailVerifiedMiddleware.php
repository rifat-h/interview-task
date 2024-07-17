<?php

namespace App\Http\Middleware;

use App\Helper\Ui\FlashMessageGenerator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->hasVerifiedEmail()) {
            FlashMessageGenerator::generate('danger', "verify your email first");
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
