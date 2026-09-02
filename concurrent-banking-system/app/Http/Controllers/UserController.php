<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResources;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    //
    public function __construct(public UserServices $userServices){}
    public function index(): JsonResponse
    {
        //
        $this->authorize('viewAny', request()->user());
        $users = User::all();
        return response()->json([
            'users' => UserResources::collection($users)
        ]);
    }

    public function store(UserRequest $request): JsonResponse
    {
        //
        $this->authorize('create', request()->user());
        $credentials = $request->validated();
        $user = $this->userServices->createUser($credentials);
        return response()->json([
            'user' => new UserResources($user),
        ],201);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        //
        $this->authorize('update', request()->user());
        $credentials = $request->validated();
        $res = $this->userServices->updateUser($user ,$credentials);
        return response()->json(
            $res
        );
    }

    public function show(User $user): JsonResponse
    {
        //
        $this->authorize('view', request()->user());
        $res = $this->userServices->showUser($user);
        return response()->json(
            $res
        );
    }
    public function destroy(User $user): JsonResponse
    {
        //
        $this->authorize('delete', request()->user());
        $res = $this->userServices->deleteUser($user);
        return response()->json(
            $res,204
        );
    }
}
