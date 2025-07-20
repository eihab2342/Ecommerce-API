<?php

namespace App\Helpers;

use Illuminate\Support\Facades\RateLimiter;

class RateLimitHelper
{
    public static function checkAndHit(string $key, int $maxAttempts = 3, int $decaySeconds = 60)
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Too Many Attempts. Please try again in ' . RateLimiter::availableIn($key) . 'seconds',
            ], 429); // 429 => Too many Requests
        }

        RateLimiter::hit($key, $decaySeconds);

        return null; // اقصد يعني عديه بنجاح وخلاص
    }

    public static function clear(string $key): void
    {
        RateLimiter::clear($key);
    }
}