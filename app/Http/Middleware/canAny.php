<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class canAny
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {

        if (!$request->user()) {
            abort(403);
        }



        if (!sizeof($permissions)|| in_array(auth()->user()->role,$permissions)) {
            abort(403);
        }



        return $next($request);


    }
}
