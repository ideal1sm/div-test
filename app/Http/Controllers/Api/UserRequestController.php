<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequestStoreRequest;
use App\Http\Requests\UserRequestUpdateRequest;
use App\Http\Resources\UserRequestResource;
use App\Http\Services\UserRequestService;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserRequestController extends Controller
{
    private UserRequestService $service;

    public function __construct(UserRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     * path="/api/v1/requests",
     * summary="Get user requests",
     * description="Get all records of user requests",
     * operationId="getUserRequests",
     * tags={"User Requests"},
     * security={{"sanctum": {} }},
     * @OA\Parameter(
     *    description="Records per pagee",
     *    in="query",
     *    name="per-page",
     *    required=false,
     *    example="15",
     * ),
     * @OA\Parameter(
     *    description="Filter by status",
     *    in="query",
     *    name="filters[status]",
     *    required=false,
     *    example="Active",
     * ),
     * @OA\Parameter(
     *    description="Filter by date created",
     *    in="query",
     *    name="filters[created-at][from]",
     *    required=false,
     *    example="22.07.2023",
     * ),
     * @OA\Parameter(
     *    description="Filter by date created",
     *    in="query",
     *    name="filters[created-at][to]",
     *    required=false,
     *    example="23.07.2023",
     * ),
     * @OA\Parameter(
     *    description="Filter by user name",
     *    in="query",
     *    name="user-name",
     *    required=false,
     *    example="Test user",
     * ),
     * @OA\Parameter(
     *    description="Filter by user email",
     *    in="query",
     *    name="user-email",
     *    required=false,
     *    example="test@gmail.com",
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="data", type="array", @OA\Items(
     *              ref="#/components/schemas/UserRequest"
     *          )),
     *         @OA\Property(property="links", type="object",
     *              @OA\Property(property="first", type="string", example="http://localhost/api/v1/requests?page=1"),
     *              @OA\Property(property="last", type="string", example="http://localhost/api/v1/requests?page=10"),
     *              @OA\Property(property="prev", type="string", example="http://localhost/api/v1/requests?page=2"),
     *              @OA\Property(property="next", type="string", example="http://localhost/api/v1/requests?page=4"),
     *          ),
     *         @OA\Property(property="meta", type="object")
     *     ),
     *       @OA\Property(property="message", type="string", example="ok"),
     *        )
     *     ),
     * )
     */

    public function index(Request $request)
    {
        return $this->success(
            $this->service
                ->getUserRequests($request)
        );
    }

    /**
     * @OA\Get(
     * path="/api/v1/requests/{user_request_id}",
     * summary="Get user request by id",
     * description="Get one record of user request by id",
     * operationId="getUserRequest",
     * tags={"User Requests"},
     * security={{"sanctum": {} }},
     * @OA\Parameter(
     *    description="User request id",
     *    in="path",
     *    name="user_request_id",
     *    required=true,
     *    example="1",
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="object", ref="#/components/schemas/UserRequest"),
     *       @OA\Property(property="message", type="string", example="ok"),
     *        )
     *     ),
     * )
     */

    public function show(UserRequest $userRequest)
    {
        return $this->success(
            new UserRequestResource($userRequest->load(['user' => function ($query) {
                $query->with('role');
            }]))
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/requests",
     * summary="Send request",
     * description="Send request",
     * operationId="sendUserRequest",
     * tags={"User Requests"},
     * security={{"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="User request info fields",
     *    @OA\JsonContent(
     *       required={"name", "email", "message"},
     *     @OA\Property(property="name", type="string", example="Test user"),
     *     @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *     @OA\Property(property="message", type="string", example="Test message"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="string", example="true"),
     *       @OA\Property(property="data", type="object",
     *         @OA\Property(property="request_id", type="int", example="5")
     *            ),
     *       @OA\Property(property="message", type="string", example="ok"),
     *        )
     *     ),
     * )
     */

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

    /**
     * @OA\Put(
     * path="/api/v1/requests/{user_request_id}",
     * summary="Resolve user request",
     * description="Resolve user request by manager",
     * operationId="resolveUserRequest",
     * tags={"User Requests"},
     * security={{"sanctum": {} }},
     * @OA\Parameter(
     *    description="User request id",
     *    in="path",
     *    name="user_request_id",
     *    required=true,
     *    example="1",
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Manager comment, that sended to mail user",
     *    @OA\JsonContent(
     *       required={"comment"},
     *     @OA\Property(property="comment", type="string", example="Your propblem has been solved!"),
     *    ),
     * ),
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

    /**
     * @OA\Delete(
     * path="/api/v1/requests/{user_request_id}",
     * summary="Delete user request",
     * description="Delete user request by manager",
     * operationId="deleteUserRequest",
     * tags={"User Requests"},
     * security={{"sanctum": {} }},
     * @OA\Parameter(
     *    description="User request id",
     *    in="path",
     *    name="user_request_id",
     *    required=true,
     *    example="1",
     * ),
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

    public function destroy(UserRequest $userRequest)
    {
        if (!$userRequest->delete())
            return $this->failure('Ошибка сервера!', 500);

        return $this->success(message: 'ok');
    }
}
