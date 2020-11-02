<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle($request, Closure $next)
   {
       
        try { 
            $user = JWTAuth::parseToken()->authenticate(); 
        } catch (Exception $e) { 
            $respone['status'] = 0; 
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){ 
                $respone['message'] = 'Token is Invalid'; 
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){ 
                $refreshed = JWTAuth::refresh(JWTAuth::getToken()); 
                header('Authorization: Bearer ' . $refreshed); 
                $user = JWTAuth::setToken($refreshed)->toUser(); 
            }else{ 
                $respone['message'] = 'Authorization Token not found'; 
            } 
            return response()->json($respone, 401); 
        } 
        return $next($request);
   }
}
