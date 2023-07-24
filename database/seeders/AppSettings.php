<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory(1)->create();
        $user = $user->first();
        $contact = \App\Models\Contact::create([
            'fullname' => 'Administrator',
            'first_name' => 'Administrator',
            'last_name' => 'Administrator',
            'email_address' => $user->email,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $user->contact_id = $contact->id;
        $user->save();
        
        DB::table('app_settings')->insert([
            'locale' => 'pt_PT',
            'timezone' => 'Europe/Lisbon',
            'login_remember_me' => 1,
            'session_expire_time' => 2,
            'updated_by' => DB::table('users')
                ->select('id')
                ->where('backoffice_access', 1)
                ->orderBy('id', 'ASC')
                ->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
