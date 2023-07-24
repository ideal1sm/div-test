<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequestStoreRequest;
use App\Http\Requests\UserRequestUpdateRequest;
use App\Http\Resources\UserRequestResource;
use App\Mail\ProfileDelete;
use App\Mail\UserRequestResolved;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class UserRequestController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)$request->get('per-page', 15);

        return $this->success(
            UserRequestResource::collection(
                UserRequest::with('user')->paginate($perPage)
            )->response()
                ->getData(true)
        );
    }

    public function show(UserRequest $userRequest)
    {
        return $this->success(
            new UserRequestResource($userRequest->load('user'))
        );
    }

    public function store(UserRequestStoreRequest $request)
    {
        $data = $request->validated();
        $token = $request->bearerToken();

        if (!empty($token)) {
            $user = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
            $data['user_id'] = $user->id;
        }

        if (!UserRequest::create($data))
            return $this->failure('Ошибка сервера!', 500);

        return $this->success(message: 'ok');
    }

    public function update(UserRequestUpdateRequest $request, UserRequest $userRequest)
    {
        $comment = $request->validated('comment');

        if (
            $userRequest->update([
                'comment' => $comment,
                'status' => UserRequestStatus::Resolved
            ])
        ) {
            $this->failure('Ошибка сервера!', 500);
        }

        Mail::queue(new UserRequestResolved($userRequest));
    }

    public function destroy(UserRequest $userRequest)
    {
        if (!$userRequest->delete())
            return $this->failure('Ошибка сервера!', 500);

        return $this->success(message: 'ok');
    }
}
