<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //Para não dar problemas na instalação inicial
        $has_app_settings_table = Cache::rememberForever('has_app_settings_table', function () {
            return \Schema::hasTable('app_settings');
        });
        if(!$has_app_settings_table){
            return;
        }

        //Definir o tempo de sessão em runtime apartir de valor configurado em base de dados
        $AppSettings = Cache::rememberForever('AppSettings', function () {
            return AppSettings::first();
        });
        if($AppSettings AND (int)$AppSettings->session_expire_time > 0){
            Config::set('session.lifetime', $AppSettings->session_expire_time * 60);
        }

        Fortify::authenticateUsing(function (Request $request) use($AppSettings) {
            $user = User::where([
                'email' => $request->email,
                'active' => 1
            ])->first();

            if ($user &&
                Hash::check($request->password, $user->password)) {

                //Matar sessão nos outros dispositivos por defeito ao fazer login (antes de iniciar esta)
                if($AppSettings AND $AppSettings->logout_other_devices === true){
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                }

                return $user;
            }
        });
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
