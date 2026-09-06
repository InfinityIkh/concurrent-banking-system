<?php

namespace App\Exceptions;

use Exception;

class InvalidTransactionAmountException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'The transaction amount must be a multiple of 50.'
        );
    }
}
