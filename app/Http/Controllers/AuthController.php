<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use App\Traits\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function __construct(protected AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $token = Auth::attempt($credentials);

        if (!$token) {
            return $this->errorResponse('User unauthorized', 401);
        }

        $user = Auth::user();
        return $this->successResponse([
            'user' => new UserResource($user),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ], 'Login Successfully', 200);
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->createUser($data);
        $token = Auth::login($user);

        return $this->successResponse([
            'user' => new UserResource($user),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ], 'Register successfully', 201);
    }

    public function logout()
    {
        Auth::logout();

        return $this->successResponse([], 'Successfully logged out');
    }

    public function refresh()
    {
        return $this->successResponse([
            'user' => new UserResource(Auth::user()),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ], 'Token refreshed successfully', 200);
    }
}
