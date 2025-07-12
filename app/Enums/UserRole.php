<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Driver = 'driver';
    case Rider = 'rider';
}
