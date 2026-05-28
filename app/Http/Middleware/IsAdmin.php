<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('api')->user();

            if ($user === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - no user found',
                ], 401);
            }

            if ($user->role_id === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no assigned role',
                ], 403);
            }

            // Verifica el id del rol del usuario autenticado
            if ((int) $user->role_id === 1) {
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'you are not an admin',
            ], 403);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('IsAdmin middleware error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error in authorization',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}
