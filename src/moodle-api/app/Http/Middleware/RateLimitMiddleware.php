<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Rate limiting: Chống DDoS và brute force attacks
     * 
     * Giới hạn: 100 requests per minute per API key
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key required'
            ], 401);
        }
        
        // Rate limit key
        $key = 'rate_limit:' . md5($apiKey);
        
        // Get current count
        $attempts = Cache::get($key, 0);
        
        // Limit: 100 requests per minute
        $maxAttempts = 100;
        $decayMinutes = 1;
        
        if ($attempts >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => 60
            ], 429);
        }
        
        // Increment counter
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        
        return $next($request);
    }
}
