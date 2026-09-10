<?php

namespace App\Enums;

enum TransactionType: string
{
    //
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdrawal';
    case TRANSFER = 'transfer';
    case REFUND = 'refund';
}
