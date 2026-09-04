<?php

namespace App\Services;

use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;

class AccountServices
{
    //
    public function getAccount(?Account $account): array
    {
        //
        if(!$account){
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Account not found'
                ]
            ];
        }
        return [
            'status' => 200,
            'body' => [
                'message' => 'Account found',
                'account' => new AccountResource($account)
            ]
        ];
    }
    public function createAccount(array $credentials ,User $user): array
    {
        //
        $account = $user->accounts()->create($credentials);
        return [
            'status' => 201,
            'body' => [
                'message' => 'Account created successfully',
                'account' => new AccountResource($account)
            ]
        ];
    }
    public function updateAccount(array $credentials ,?Account $account): array
    {
        //
        if(!$account) {
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Account not found'
                ]
            ];
        }
        $account->update($credentials);
        return [
            'status' => 200,
            'body' => [
                'message' => 'Account updated successfully',
                'account' => new AccountResource($account)
            ]
        ];
    }
    public function deleteAccount(?Account $account): array
    {
        if(!$account) {
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Account not found'
                ]
            ];
        }
        $account->delete();
        return [
            'status' => 200,
            'body' => [
                'message' => 'Account deleted successfully'
            ]
        ];
    }

}
