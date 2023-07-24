<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {

        //TODO: Popular primeira empresa de grupo
        //TODO: Popular primeiro utilizador
         //\App\Models\User::factory(50000)->create();
        $this->call([
            AppSettings::class,
        ]);
        Artisan::call('populate:countries');
        Artisan::call('populate:DistritosConcelhosFreguesias');
    }
}
