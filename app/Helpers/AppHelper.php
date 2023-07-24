<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppHelper
{

    static function isBackoffice(User $user, Request $request): bool
    {
        return (Str::startsWith($request->path(), 'backoffice') AND $user->backoffice_access);
    }

}
