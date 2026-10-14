<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Sales = 'sales';
    case Admin = 'admin';
}
