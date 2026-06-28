<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    /**
     * Chỉ cho phép requests từ IPs được whitelist
     * (Optional - dùng cho production với security cao)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get allowed IPs from config
        $allowedIps = config('security.allowed_ips', []);
        
        // If whitelist is empty, allow all (disabled)
        if (empty($allowedIps)) {
            return $next($request);
        }
        
        $clientIp = $request->ip();
        
        // Check if IP is allowed
        if (!in_array($clientIp, $allowedIps)) {
            \Log::channel('security')->warning('Blocked request from unauthorized IP', [
                'ip' => $clientIp,
                'url' => $request->fullUrl(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Access denied: IP not whitelisted'
            ], 403);
        }
        
        return $next($request);
    }
}
