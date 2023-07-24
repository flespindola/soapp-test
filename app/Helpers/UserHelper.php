<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserHelper
{

    static public function getUserDepartmentsIds(User $user)
    {
        return Cache::rememberForever('user_departments_' . $user->id, function() use ($user){
            return $user->getDepartments()->pluck('id')->toArray();
        });
    }

    public static function deleteAllUsersDepartmentsCache()
    {
        $users = User::select('id')->get();
        foreach ($users as $user) {
            Cache::delete('user_departments_' . $user->id);
        }
    }

}
