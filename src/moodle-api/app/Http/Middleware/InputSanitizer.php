<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizer
{
    /**
     * Sanitize input để chống XSS, SQL Injection, etc.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove potential XSS
                $value = strip_tags($value);
                
                // Remove SQL injection attempts
                $value = str_replace(['--', ';', '/*', '*/', 'xp_', 'sp_'], '', $value);
                
                // Trim whitespace
                $value = trim($value);
            }
        });
        
        // Replace request input with sanitized version
        $request->merge($input);
        
        // Check for suspicious patterns
        $suspicious = $this->detectSuspiciousPatterns($request);
        if ($suspicious) {
            \Log::channel('security')->warning('Suspicious input detected', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'input' => $input,
            ]);
        }
        
        return $next($request);
    }
    
    /**
     * Detect common attack patterns
     */
    private function detectSuspiciousPatterns(Request $request): bool
    {
        $input = json_encode($request->all());
        
        $patterns = [
            '/(<script|javascript:|onerror=|onload=)/i',  // XSS
            '/(union.*select|insert.*into|delete.*from)/i', // SQL Injection
            '/(\.\.|\/etc\/passwd|\/bin\/bash)/i',        // Path traversal
            '/(eval\(|base64_decode|exec\()/i',            // Code injection
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}
