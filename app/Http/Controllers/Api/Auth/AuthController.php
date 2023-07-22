<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return $this->success([
            'token' => $user->createToken($data['email'])->plainTextToken,

        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!$user = User::where('email', $data['email'])->first())
            return $this->failure('Неверная почта или пароль!');

        if (!Hash::check($data['password'], $user->password))
            return $this->failure('Неверная почта или пароль!');

        return $this->success([
            'token' => $user->createToken($data['email'])->plainTextToken,

        ]);
    }

    public function logout()
    {
        if (!Auth::user()->currentAccessToken()->delete())
            return $this->failure('Ошибка сервера!');

        return $this->success(message: 'ok');
    }
}
