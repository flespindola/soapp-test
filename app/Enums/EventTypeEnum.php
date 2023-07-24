<?php

namespace App\Enums;

enum EventTypeEnum: string
{
    case Event = 'event';
    case Task = 'task';
    case Meeting = 'meeting';
}
