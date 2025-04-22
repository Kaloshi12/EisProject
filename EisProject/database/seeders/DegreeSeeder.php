<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeSeeder extends Seeder
{
    public function run()
    {
        DB::table('degrees')->insert([
            ['name' => 'Economics', 'cost' => 2500.0, 'department_id' => 5, 'is_graduated' => false],
            ['name' => 'PhD in Economics', 'cost' => 3000.0, 'department_id' => 5, 'is_graduated' => true],
            ['name' => 'Business Administration', 'cost' => 2500.0, 'department_id' => 6, 'is_graduated' => false],
            ['name' => 'Master of Science in Business Administration', 'cost' => 1950.0, 'department_id' => 6, 'is_graduated' => true],
            ['name' => 'Professional Master in Business Administration', 'cost' => 1950.0, 'department_id' => 6, 'is_graduated' => true],
            ['name' => 'PhD in Business Administration', 'cost' => 3000.0, 'department_id' => 6, 'is_graduated' => true],
            ['name' => 'Business Informatics', 'cost' => 3000.0, 'department_id' => 6, 'is_graduated' => false],
            ['name' => 'International Marketing and Logistics Management', 'cost' => 2500.0, 'department_id' => 4, 'is_graduated' => false],
            ['name' => 'Banking and Finance', 'cost' => 2500.0, 'department_id' => 4, 'is_graduated' => false],
            ['name' => 'Master of Science in Banking and Finance', 'cost' => 1950.0, 'department_id' => 4, 'is_graduated' => true],
            ['name' => 'Banking and Finance (Albanian)', 'cost' => 1750.0, 'department_id' => 4, 'is_graduated' => false],
            ['name' => 'Software Engineering', 'cost' => 4000.0, 'department_id' => 3, 'is_graduated' => false],
            ['name' => 'Integrated study program Architecture', 'cost' => 3800.0, 'department_id' => 1, 'is_graduated' => false],
            ['name' => 'Master of Science in Architecture', 'cost' => 3800.0, 'department_id' => 1, 'is_graduated' => true],
            ['name' => 'PhD in Architecture', 'cost' => 3500.0, 'department_id' => 1, 'is_graduated' => true],
            ['name' => 'Civil Engineering', 'cost' => 3500.0, 'department_id' => 2, 'is_graduated' => false],
            ['name' => 'Master in Civil Engineering', 'cost' => 2450.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'PhD in Civil Engineering', 'cost' => 3500.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'Master in Civil Engineering: Structural Engineering', 'cost' => 2450.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'Master in Civil Engineering: Construction Management', 'cost' => 2450.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'Master in Civil Engineering: Water Resources Engineering', 'cost' => 2450.0, 'department_id' =>2 , 'is_graduated' => true],
            ['name' => 'Master in Civil Engineering: Construction Materials Engineering', 'cost' => 2450.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'Master in Disaster Risk Management and Fire Safety in Civil Engineering', 'cost' => 2450.0, 'department_id' => 2, 'is_graduated' => true],
            ['name' => 'Master in Computer Engineering', 'cost' => 2450.0, 'department_id' => 3, 'is_graduated' => true],
            ['name' => 'PhD in Computer Engineering', 'cost' => 3500.0, 'department_id' => 3, 'is_graduated' => true],
            ['name' => 'Master of Science in Computer Engineering', 'cost' => 2450.0, 'department_id' => 3, 'is_graduated' => true],
            ['name' => 'Electronics and Digital Communications Engineering', 'cost' => 3500.0, 'department_id' => 3, 'is_graduated' => false],
            ['name' => 'Master of Science in Electronics and Communication Engineering', 'cost' => 2450.0, 'department_id' => 3, 'is_graduated' => true],
            ['name' => 'Integrated study program in Law', 'cost' => 2800.0, 'department_id' => 7, 'is_graduated' => false],
            ['name' => 'Political Science and International Relations', 'cost' => 2500.0, 'department_id' => 8, 'is_graduated' => false],
            ['name' => 'Master of Science in Political Science and International Relations', 'cost' => 1950.0, 'department_id' => 8, 'is_graduated' => true],
            ['name' => 'Professional Master in Political Science and International Relations', 'cost' => 1950.0, 'department_id' => 8, 'is_graduated' => true],
            ['name' => 'PhD in Political Science and International Relations', 'cost' => 1950.0, 'department_id' => 8, 'is_graduated' => true],
            ['name' => 'Computer Engineer', 'cost' => 4000.0, 'department_id' => 3, 'is_graduated' => false]
        ]);
    }
}