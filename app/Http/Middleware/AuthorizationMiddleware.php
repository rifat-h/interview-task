<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helper\Ui\FlashMessageGenerator;
use Spatie\Permission\Models\Permission;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // get current url name
        $currentRouteName = $request->route()->getName();
        // dd($currentRouteName);

        // get curren route permission
        $currentRoutePermission = Permission::where('url', $currentRouteName)->first();

        if ($currentRoutePermission) {
            // get current user
            $user = auth()->user();

            // check if user has permission to access current url
            if ($user->can($currentRoutePermission->name)) {
                return $next($request);
            }else{
                FlashMessageGenerator::generate('danger', 'You Do Not Have Access To This Page');
                return back();
            }
        }

        return $next($request);
    }
}
