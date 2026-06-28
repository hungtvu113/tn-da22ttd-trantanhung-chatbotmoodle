<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Cap do quyen: cao hon bao gom quyen cua thap hon.
     * admin (3) > teacher (2) > student (1)
     */
    private const ROLE_LEVELS = [
        'student' => 1,
        'teacher' => 2,
        'admin'   => 3,
    ];

    /**
     * Xac thuc API key va kiem tra quyen theo vai tro.
     *
     * Dung trong route: ->middleware('api.key:student' | 'api.key:teacher' | 'api.key:admin')
     * Tham so la quyen TOI THIEU can co de truy cap endpoint.
     */
    public function handle(Request $request, Closure $next, string $minRole = 'student'): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return $this->deny('Unauthorized: Missing API Key', 401);
        }

        $role = $this->resolveRole($apiKey);
        if ($role === null) {
            return $this->deny('Unauthorized: Invalid API Key', 401);
        }

        $required = self::ROLE_LEVELS[$minRole] ?? PHP_INT_MAX;
        $current  = self::ROLE_LEVELS[$role] ?? 0;

        if ($current < $required) {
            return $this->deny(
                "Forbidden: endpoint nay yeu cau quyen '{$minRole}' tro len (key cua ban la '{$role}')",
                403
            );
        }

        // Gan role vao request de controller dung neu can.
        $request->attributes->set('api_role', $role);

        return $next($request);
    }

    /**
     * Tim vai tro tuong ung voi API key. Tra null neu khong khop.
     */
    private function resolveRole(string $apiKey): ?string
    {
        $keys = config('services.api_keys', []);

        foreach (['admin', 'teacher', 'student'] as $role) {
            if (in_array($apiKey, $keys[$role] ?? [], true)) {
                return $role;
            }
        }

        // Key cu (legacy/chatbot) -> coi nhu admin.
        if (in_array($apiKey, $keys['legacy'] ?? [], true)) {
            return 'admin';
        }

        return null;
    }

    private function deny(string $message, int $code): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
