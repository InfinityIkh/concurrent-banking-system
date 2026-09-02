<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    //
    public function __construct(public AuthServices $authServices){}

    public function register(UserRequest $request): JsonResponse
    {
        //
        $credentials = $request->validated();
        $this->authServices->register($credentials);
        return response()->json([

        ],201);
    }

    public function login(UserRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $resultat = $this->authServices->login($credentials);
        return response()->json([
           $resultat['body']
        ],$resultat['status']);
    }

    public function logout(): JsonResponse
    {
        //
        auth('api')->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ],204);
    }

    //public function refresh()
    //{
    //    return $this->authServices->respondWithToken(auth()->refresh());
    //}
}
