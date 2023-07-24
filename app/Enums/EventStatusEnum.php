<?php

namespace App\Enums;

enum EventStatusEnum: string
{
    case Pending = 'pending';
    case Completed = 'completed';
}
