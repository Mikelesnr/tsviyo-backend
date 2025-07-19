<?php

namespace App\Enums;

enum RideStatus: string
{
    case REQUESTED = 'requested';
    case ACCEPTED = 'accepted';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
