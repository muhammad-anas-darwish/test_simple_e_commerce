<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->unauthorized();
        }

        $user = Auth::user();
        return response()->ok([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->createUser($data);
        $token = Auth::login($user);

        return response()->created(
            [
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ],
            'User created successfully',
        );
    }

    public function logout()
    {
        Auth::logout();

        return response()->ok([], 'Successfully logged out');
    }

    public function refresh()
    {
        return response()->ok([
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ]);
    }
}
