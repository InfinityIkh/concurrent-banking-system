<?php

namespace App\Services;

use App\Http\Resources\UserResources;
use App\Models\User;

class UserServices
{
    //
    public function createUser(array $credentials): User
    {
        //
        return User::create($credentials);
    }

    public function updateUser(?User $user, array $credentials): array
    {
        //
        if(!$user){
            return [
                'status' => 404,
                'body' => [
                    "message" => "User not found",
                ]
            ];
        }

        $user->update($credentials);

        return [
            'status' => 200,
            'body' => [
                'message' => "User updated successfully",
                'user' => new UserResources($user),
            ]
        ];
    }

    public function deleteUser(?User $user): array
    {
        //
        if(!$user){
            return [
                'status' => 404,
                'body' => [
                    "message" => "User not found",
                ]
            ];
        }
        $user->delete();
        return [
            'status' => 204,
            'body' => [
                "message" => "User deleted successfully",
            ]
        ];
    }

    public function showUser(?User $user): array
    {
        if(!$user){
            return [
                'status' => 404,
                'body' => [
                    "message" => "User not found",
                ]
            ];
        }
        return [
            'status' => 200,
            'body' => [
                "message" => "User retrieved successfully",
                "user" => new UserResources($user)
            ]
        ];
    }
}
