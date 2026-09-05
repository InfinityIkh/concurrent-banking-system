<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    //
    use softDeletes;
    protected $fillable = [
        'user_id',
        'status',
        'currency_id',
        'account_number'
    ];

    protected $casts = [
        'status' => AccountStatus::class,
    ];

    protected static function booted(): void
    {
        //
        static::creating(function ($account) {
            $account->account_number = Account::generateAccountNumber();
        });
    }
    private static function generateAccountNumber(): string
    {
        //
        do{
            $number = 'IKH-'.rand(1000, 9999).'-'.rand(1000, 9999);
        }while(self::where('account_number', $number)->exists());

        return $number;
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }
}
