<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //User::class => UserPolicy::class,
        //'App\Models\Company' => CompanyPolicy::class, //Policy Auto-Discovery
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
