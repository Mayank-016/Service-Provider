<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLoginRequests
{
    protected $maxAttempts = 3;
    protected $lockoutMinutes = 180; // 3 hours

    public function handle(Request $request, Closure $next)
    {
        $email = $request->input('email');

        if (!$email) {
            return $next($request);
        }

        $attemptsCacheKey = 'login_attempts_' . $email;
        $lockoutCacheKey = 'login_lockout_' . $email;

        // Check if locked out
        if (Cache::has($lockoutCacheKey)) {
            $lockoutExpiresAt = Cache::get($lockoutCacheKey);
            $remainingSeconds = Carbon::now()->diffInSeconds(Carbon::parse($lockoutExpiresAt), false);

            if ($remainingSeconds > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Your account is locked due to too many failed login attempts. Try again after " . gmdate("H:i:s", $remainingSeconds),
                    'locked_until' => $lockoutExpiresAt,
                ], Response::HTTP_LOCKED);
            } else {
                Cache::forget($lockoutCacheKey);
                Cache::forget($attemptsCacheKey);
            }
        }

        // Increment attempts on every request (only if not locked)
        $attempts = Cache::get($attemptsCacheKey, 0) + 1;
        Cache::put($attemptsCacheKey, $attempts, now()->addMinutes($this->lockoutMinutes));

        if ($attempts > $this->maxAttempts) {
            $lockoutUntil = now()->addMinutes($this->lockoutMinutes);
            Cache::put($lockoutCacheKey, $lockoutUntil->toDateTimeString(), $this->lockoutMinutes * 60);

            return response()->json([
                'success' => false,
                'message' => "Your account is locked due to too many failed login attempts. Try again after " . ($this->lockoutMinutes / 60) . " hours",
                
            ], Response::HTTP_LOCKED);
        }

        return $next($request);
    }
}
