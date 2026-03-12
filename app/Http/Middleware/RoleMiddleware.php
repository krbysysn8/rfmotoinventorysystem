<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: middleware('role:admin') or middleware('role:admin,staff')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // Support both relationship-based role (role->role_name) and direct column (role)
        $userRole = null;
        if ($user->relationLoaded('role') && $user->role) {
            $userRole = $user->role->role_name;
        } elseif (isset($user->role) && is_string($user->role)) {
            $userRole = $user->role;
        } else {
            // Lazy-load the relationship if not already loaded
            try {
                $user->loadMissing('role');
                $userRole = $user->role?->role_name;
            } catch (\Throwable $e) {
                // Fallback: check direct column
                $userRole = $user->getAttributes()['role'] ?? null;
            }
        }

        if (!$userRole || !in_array($userRole, $roles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}