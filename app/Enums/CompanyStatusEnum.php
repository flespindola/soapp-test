<?php

namespace App\Enums;

enum CompanyStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
