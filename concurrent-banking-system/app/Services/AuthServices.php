<?php

namespace App\Services;

use App\Http\Resources\UserResources;
use App\Models\User;

class AuthServices
{
    //
    public function register(array $credentials): array
    {
        //
        $user = User::create($credentials);

        return [
            'message' => 'User Registered successfully',
            'user' => new UserResources($user)
        ];
    }

    public function login(array $credentials): array
    {
        if (! $token = auth('api')->attempt($credentials)) {
            return [
                'status' => 401,
                'body' => [
                    'error' => 'Unauthorized'
                ]
            ];
        }
        return [
            'status' => 200,
            'body' => [
                'message' => 'User Login successfully',
                'data' => $this->respondWithToken($token),
            ]
        ];
    }

    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }
}
