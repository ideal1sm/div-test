<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Filters\EntityFilter;
use App\Http\Filters\UserRequestFilter;
use App\Http\Requests\UserRequestStoreRequest;
use App\Http\Requests\UserRequestUpdateRequest;
use App\Http\Resources\UserRequestResource;
use App\Mail\UserRequestResolved;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class UserRequestController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)$request->get('per-page', 15);
        $filters = $request->get('filters');
        $userRequestQuery = UserRequest::query();

        if (!empty($filters)) {
            $filter = new UserRequestFilter($filters);
            $userRequestQuery->filter($filter);
        }

        return $this->success(
            UserRequestResource::collection(
                $userRequestQuery->with('user')
                    ->paginate($perPage)
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

        if (!$userRequest = UserRequest::create($data))
            return $this->failure('Ошибка сервера!', 500);

        /* Передать пользователю номер заявки */
        return $this->success(message: [
            'request_id' => $userRequest->id
        ]);
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

        return $this->success('ok');
    }

    public function destroy(UserRequest $userRequest)
    {
        if (!$userRequest->delete())
            return $this->failure('Ошибка сервера!', 500);

        return $this->success(message: 'ok');
    }
}
