<?php

namespace App\Http\Middleware;

use Closure;

class ReqPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission)
    {
        // Pre-Middleware Action

        if(!in_array($permission,$request->permissions))
        {
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Unauthorized request'],403);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
