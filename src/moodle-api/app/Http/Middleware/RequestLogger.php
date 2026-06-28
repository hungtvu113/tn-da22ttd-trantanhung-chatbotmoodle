<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Log tất cả API requests để audit và detect suspicious activity
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Process request
        $response = $next($request);
        
        // Calculate response time
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log request details
        Log::channel('api')->info('API Request', [
            'timestamp' => now()->toIso8601String(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'api_key' => $this->maskApiKey($request->header('X-API-Key')),
            'status' => $response->status(),
            'duration_ms' => $duration,
        ]);
        
        // Detect suspicious activity
        if ($response->status() === 401) {
            Log::channel('security')->warning('Unauthorized API access attempt', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'api_key' => $this->maskApiKey($request->header('X-API-Key')),
            ]);
        }
        
        return $response;
    }
    
    /**
     * Mask API key for logging (security best practice)
     */
    private function maskApiKey(?string $apiKey): string
    {
        if (!$apiKey) {
            return 'none';
        }
        
        if (strlen($apiKey) <= 8) {
            return str_repeat('*', strlen($apiKey));
        }
        
        return substr($apiKey, 0, 4) . str_repeat('*', strlen($apiKey) - 8) . substr($apiKey, -4);
    }
}
