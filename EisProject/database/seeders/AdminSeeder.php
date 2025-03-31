<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'institution_number'=>'00907654',
            'id_cart_number'=>'D87654321E',
            'name'=>'admin',
            'surname'=>'admin',
            'email' => 'admin@epoka.edu.al',
            'secondary_email'=>'admin@gmail.com',
            'birthdate' => fake()->dateTimeBetween('-50 years', '-30 years')->format('Y-m-d'),
            'nationality'=>'Albania',
            'gender'=>'male',
            'blood_group'=>'A-',
            'civil_status'=>'married',
            'role_id'=>1,
            'password'=>bcrypt('12345678'),
            'created_at'=>now(),
            'updated_at'=>now()  
        ]);
    }
}
