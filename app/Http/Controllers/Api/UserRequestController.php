<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Filters\UserRequestFilter;
use App\Http\Requests\UserRequestStoreRequest;
use App\Http\Requests\UserRequestUpdateRequest;
use App\Http\Resources\UserRequestResource;
use App\Http\Services\UserRequestService;
use App\Mail\UserRequestResolved;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class UserRequestController extends Controller
{
    private UserRequestService $service;
    public function __construct(UserRequestService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->success(
            $this->service
                ->getUserRequests($request)
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
        try {
            $userRequestId = $this->service->storeUserRequest($request->validated(), $request->bearerToken());
        } catch (\Error $error) {
            return $this->failure($error->getMessage(), 500);
        }

        return $this->success([
            'request_id' => $userRequestId,

        ]);
    }

    public function update(UserRequestUpdateRequest $request, UserRequest $userRequest)
    {
        $data = $request->validated();

        try {
            $message = $this->service->updateUserRequest($data, $userRequest);
        } catch (\Error $error) {
            return $this->failure($error->getMessage(), 500);
        }

        return $this->success(message: $message);
    }

    public function destroy(UserRequest $userRequest)
    {
        if (!$userRequest->delete())
            return $this->failure('Ошибка сервера!', 500);

        return $this->success(message: 'ok');
    }
}
