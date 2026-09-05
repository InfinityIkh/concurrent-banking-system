<?php

namespace App\Enums;

enum TransactionType: string
{
    //
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';
    case REFUND = 'refund';
}
