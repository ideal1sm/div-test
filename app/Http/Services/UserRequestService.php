<?php

namespace App\Http\Services;

use App\Enums\UserRequestStatus;
use App\Http\Filters\UserRequestFilter;
use App\Http\Resources\UserRequestResource;
use App\Mail\UserRequestResolved;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class UserRequestService
{
    public function getUserRequests(Request $request): array
    {
        $perPage = (int)$request->get('per-page', 15);
        $filters = $request->get('filters');
        $userRequestQuery = UserRequest::query();

        if (!empty($filters)) {
            $filter = new UserRequestFilter($filters);
            $userRequestQuery->filter($filter);
        }

        return UserRequestResource::collection(
            $userRequestQuery->with('user')
                ->paginate($perPage)
        )->response()->getData(true);
    }

    public function storeUserRequest(array $data, string|null $token): int
    {
        if (!empty($token)) {
            $user = PersonalAccessToken::findToken($token)->tokenable;
            $data['user_id'] = $user->id;
        }

        if (!$userRequest = UserRequest::create($data))
            throw new \Error('Ошибка сервера');

        /* Передать пользователю номер заявки */
        return $userRequest->id;
    }

    public function updateUserRequest(array $data, UserRequest $userRequest): string
    {
        if (!$userRequest->update([
            'comment' => $data['comment'],
            'status' => UserRequestStatus::Resolved
        ])) {
            throw new \Error('Ошибка сервера!');
        }

        Mail::queue(new UserRequestResolved($userRequest));

        return 'ok';
    }
}
