<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            // Attempt to authenticate the user with the token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Token has expired; try to refresh it
            try {
                // Refresh the token
                $newToken = JWTAuth::refresh(JWTAuth::getToken());

                // Proceed with the request and capture the response
                $response = $next($request);

                // Set the new token in the response header
                return $response->header('Authorization', 'Bearer ' . $newToken);
            } catch (JWTException $e) {
                // If refresh fails, token is invalid or expired
                return response()->json(['message' => 'Token expired, please log in again'], 401);
            }
        } catch (JWTException $e) {
            // Token is invalid
            return response()->json(['message' => 'Token invalid'], 401);
        }

        // If token is valid, proceed with the request
        return $next($request);
    }
}
