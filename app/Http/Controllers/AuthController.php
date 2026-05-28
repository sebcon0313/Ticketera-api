<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LoginRequest;
use App\Domain\User\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Domain\User\Services\AuthService;
use App\Domain\User\Models\User;

class AuthController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            $this->userRepository->create([
                'name' => $request->validated('name'),
                'role_id' => $request->validated('role_id'),
                'email' => $request->validated('email'),
                'password' => bcrypt($request->validated('password')),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $input = $request->validated();
            if (isset($input['password'])) {
                $safeInput = $input;
                unset($safeInput['password']);
            } else {
                $safeInput = $input;
            }

            $result = $this->authService->login($input);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'token' => $result['token'],
                'user' => $result['user'],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $safeInput ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error during authentication',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function getUser(): JsonResponse
    {
        try 
        {
            $user = $this->authService->user();

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);

        }
        catch (\Exception $e)
        {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $safeInput ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
        
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error during logout',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}
