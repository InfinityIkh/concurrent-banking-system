<?php

namespace App\Services;

use App\Enums\AccountStatus;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountServices
{
    //
    public function activateAccount(int $id): array
    {
        //
        return DB::transaction(function () use ($id) {
            $lockedAccount = Account::where('id' ,$id)->lockForUpdate()->first();
            if($lockedAccount->status === AccountStatus::active){
                return [
                    'status' => 400,
                    'body' => [
                        'message' => 'Account already activated'
                    ]
                ];
            }
            $lockedAccount->update([
                'status' => AccountStatus::active
            ]);
            return [
                'status' => 200,
                'body' => [
                    'message' => 'Account activated successfully',
                    'account' => new AccountResource($lockedAccount)
                ]
            ];
        });
    }
    public function deactivateAccount(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $lockedAccount = Account::where('id' ,$id)->lockForUpdate()->first();
            if($lockedAccount->status === AccountStatus::inactive){
                return [
                    'status' => 400,
                    'body' => [
                        'message' => 'Account already deactivated'
                    ]
                ];
            }
            $lockedAccount->update([
                'status' => AccountStatus::inactive
            ]);
            return [
                'status' => 200,
                'body' => [
                    'message' => 'Account deactivated successfully',
                ]
            ];
        });
    }
    public function closeAccount(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $lockedAccount = Account::where('id' ,$id)->lockForUpdate()->first();
            if($lockedAccount->status === AccountStatus::closed){
                return [
                    'status' => 400,
                    'body' => [
                        'message' => 'Account already closed'
                    ]
                ];
            }
            $lockedAccount->update([
                'status' => AccountStatus::closed
            ]);
            return [
                'status' => 200,
                'body' => [
                    'message' => 'Account successfully closed'
                ]
            ];
        });
    }
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
        $account = $user->account()->create($credentials);
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
