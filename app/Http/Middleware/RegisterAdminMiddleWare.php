<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterAdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenKey = config('admin.token_key');
        $validToken = config('admin.token');

        // Check first in headers, then in POST body
        $providedToken = $request->header($tokenKey) ?? $request->post($tokenKey);

        if (!$providedToken || $providedToken !== $validToken) {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
