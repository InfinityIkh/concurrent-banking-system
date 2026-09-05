<?php

namespace App\Enums;

enum AccountStatus: string
{
    //
    case active = 'active';
    case inactive = 'inactive';
    case closed = 'closed';
}
