<?php

namespace App\Models;

use App\Enums\TransactionAccountRole;
use Illuminate\Database\Eloquent\Model;

class TransactionAccount extends Model
{
    //
    protected $table = 'transaction_accounts';
    protected $fillable = [
        'transaction_id',
        'account_id',
        'role',
        'balance_before',
        'balance_after',
    ];
    protected $casts = [
        'role' => TransactionAccountRole::class,
    ];
}
