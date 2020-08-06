<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;


class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->headers->get('authorization');
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'status'=>false,
                'message' => 'Token not provided.'
            ], 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'status'=>false,
                'message' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'status'=>false,
                'message' => 'An error while decoding token.'
            ], 400);
        }

        $user = User::find($credentials->sub);

        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        $request->permissions=$credentials->data;

        return $next($request);
    }
}
