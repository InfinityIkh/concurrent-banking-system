<?php

namespace App\Enums;

enum TransactionAccountRole: string
{
    //
    case SENDER = 'sender';
    case RECEIVER = 'receiver';
}
