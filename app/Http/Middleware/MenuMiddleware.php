<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class MenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // get and cache permisssion
        $permissions = Cache::remember('permissions', 3600, function () {
            return Permission::all();
        });

        // get all menus
        session(['menus' => $permissions]);

        return $next($request);
    }
}
