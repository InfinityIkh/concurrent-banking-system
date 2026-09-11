<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    public function refund(User $user ,Transaction $transaction): bool
    {
        return $user->id === $transaction->account_id or $user->role === UserRole::ADMIN;
    }
}
