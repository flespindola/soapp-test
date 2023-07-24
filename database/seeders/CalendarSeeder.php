<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Calendar;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Calendar::create([
            'user_id' => auth()->user() != null ? auth()->user()->id : 1,
            'title' => 'Default',
            'color' => '2600ff',
            'is_default' => true,
        ]);
    }
}
