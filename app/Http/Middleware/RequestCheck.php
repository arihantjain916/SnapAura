<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class RequestCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!$request->hasHeader('SECRET_KEY')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing SECRET_KEY header',
            ], 400);
        }

        return $next($request);
    }
}
