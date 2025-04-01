<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultySeeder extends Seeder
{
    public function run()
    {
        DB::table('Faculty')->insert([
            ['name' => 'Faculty of Architecture and Engineering'],
            ['name' => 'Faculty of Economics and Administrative Sciences'],
            ['name' => 'Faculty of Law and Social Sciences'],
        ]);
    }
}
