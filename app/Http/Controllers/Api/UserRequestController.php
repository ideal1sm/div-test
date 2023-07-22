<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequestStoreRequest;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class UserRequestController extends Controller
{
    public function store(UserRequestStoreRequest $request)
    {
        $data = $request->validated();
        $token = $request->bearerToken();

        if (!empty($token)) {
            $user = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
            $data['user_id'] = $user->id;
        }

        UserRequest::create($data);
    }
}
