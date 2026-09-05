<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use softDeletes;
    //
    protected $table = 'transactions';
    protected $fillable = [
        'account_id',
        'destination_account_id',
        'amount',
        'balance_before',
        'balance_after',
        'reference',
        'status',
        'type'
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'type' => TransactionType::class
    ];

    protected static function booted(): void
    {
        static::creating(function ($transaction) {
            $transaction->reference = Transaction::generateReferenceNumber();
            $transaction->status = TransactionStatus::PENDING;
        });
    }
    private static function generateReferenceNumber(): string
    {
        do{
            $reference = 'REF-'.\Illuminate\Support\now()->toFormattedDateString().'-'.rand(1000,9999).'-'.rand(1000,9999);
        }while(self::where('reference', $reference)->exists());
        return $reference;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class , 'account_id');
    }
    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class ,'destination_account_id');
    }
}
