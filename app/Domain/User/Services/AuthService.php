<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(array $credentials): array
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'success' => false,
                    'token' => null,
                    'user' => null,
                    'message' => 'Invalid credentials',
                ];
            }

            $user = JWTAuth::user();

            return [
                'success' => true,
                'token' => $token,
                'user' => $user,
                'message' => null,
            ];
        } catch (JWTException $e) {
            Log::error('JWT token error: ' . $e->getMessage());

            return [
                'success' => false,
                'token' => null,
                'user' => null,
                'message' => 'Could not create token',
            ];
        }
    }

    public function logout(): void
    {
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::invalidate($token);
        }
    }

    public function user(): ?User
    {
        return JWTAuth::user();
    }
}
