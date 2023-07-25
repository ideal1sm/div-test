<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/auth/register",
     * summary="Regster user",
     * description="",
     * operationId="userRegister",
     * tags={"Auth"},
     *
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *     required={"name", "email", "password"},
     *     @OA\Property(property="name", type="string", example="Test Ivan"),
     *     @OA\Property(property="email", type="string", format="email", example="test.ivan@gmail.com"),
     *     @OA\Property(property="password", type="string", format="password", example="123qwe"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="object", @OA\Property (
     *          property="token", type="string", example="5|RJEhSxb8LbNtaBDN6CCKlDC0nIhVvWrhOIGq53OY"
     *     )),
     *       @OA\Property(property="message", type="string", example=""),
     *        )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The email has already been taken."),
     *       @OA\Property(property="errors", type="object", @OA\Property (
     *          property="email", type="array", @OA\Items(
     *          type="string",
     *          example="The email has already been taken."))
     *       )
     *     )
     * )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return $this->success([
            'token' => $user->createToken($data['email'])->plainTextToken,

        ]);
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *     @OA\Property(property="email", type="string", format="email", example="test.ivan@gmail.com"),
     *     @OA\Property(property="password", type="string", format="password", example="123qwe"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="object", @OA\Property (
     *          property="token", type="string", example="5|RJEhSxb8LbNtaBDN6CCKlDC0nIhVvWrhOIGq53OY"
     *     )),
     *       @OA\Property(property="message", type="string", example=""),
     *        )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="false"),
     *       @OA\Property(property="message", type="string", example="Неверная почта или пароль!"),
     *    )
     * )
     * )
     */
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

    /**
     * @OA\Get(
     * path="/api/v1/auth/logout",
     * summary="Logout",
     * description="Logout",
     * operationId="authLogout",
     * tags={"Auth"},
     * security={{"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="array", @OA\Items(type="string"), example="[]"),
     *       @OA\Property(property="message", type="string", example="ok"),
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="false"),
     *       @OA\Property(property="message", type="string", example="Ошибка сервера!"),
     *    )
     * )
     * )
     */

    public function logout()
    {
        if (!Auth::user()->currentAccessToken()->delete())
            return $this->failure('Ошибка сервера!');

        return $this->success(message: 'ok');
    }
}
