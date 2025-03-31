<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            ['faculty_id' => 1, 'name' => 'Architecture'],
            ['faculty_id' => 1, 'name' => 'Civil Engineering'],
            ['faculty_id' => 1, 'name' => 'Computer Engineering'],
            ['faculty_id' => 2, 'name' => 'Banking and Finance'],
            ['faculty_id' => 2, 'name' => 'Economics'],
            ['faculty_id' => 2, 'name' => 'Business Administration'],
            ['faculty_id' => 3, 'name' => 'Law'],
            ['faculty_id' => 3, 'name' => 'Political Science and International Relations'],
            ['faculty_id' => 3, 'name' => 'Center of European Studies'],
        ]);
    }
}
