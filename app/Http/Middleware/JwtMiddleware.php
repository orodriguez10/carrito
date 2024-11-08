<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Facades\JWTAuth;
use Closure;
use Exception;

class JwtMiddleware
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
            JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            return response(['status' => 'Token inválido'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response(['status' => 'Token inválido'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response(['status' => 'El token ha expirado'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response(['status' => 'El token no ha sido encontrado'], 401);
        } catch (Exception $e) {
            return response(['status' => 'El token no ha sido encontrado'], 401);
        }
        return $next($request);
    }
}
