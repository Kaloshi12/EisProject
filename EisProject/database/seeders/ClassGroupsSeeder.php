<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassGroupsSeeder extends Seeder
{
    public function run()
    {
        $degreeIds = DB::table('degrees')->pluck('id', 'name');
        dd($degreeIds->keys());

        $classGroups = [
            ['name' => 'BA BAF 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance'], 'year_study' => 1],
            ['name' => 'BA BAF 2 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance'], 'year_study' => 2],
            ['name' => 'BA BAF 3 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance'], 'year_study' => 3],
            ['name' => 'BA BAFAL 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance (Albanian)'], 'year_study' => 1],
            ['name' => 'BA BAFAL 2 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance (Albanian)'], 'year_study' => 2],
            ['name' => 'BA BAFAL 3 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Banking and Finance (Albanian)'], 'year_study' => 3],
            ['name' => 'BA BUS 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Business Administration'], 'year_study' => 1],
            ['name' => 'BA BUS 2 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Business Administration'], 'year_study' => 2],
            ['name' => 'BA BUS 3 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Business Administration'], 'year_study' => 3],
            ['name' => 'BA IML 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['International Marketing and Logistics Management'], 'year_study' => 1],
            ['name' => 'BA IML 2 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['International Marketing and Logistics Management'], 'year_study' => 2],
            ['name' => 'BA IML 3 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['International Marketing and Logistics Management'], 'year_study' => 3],
            ['name' => 'BA SWE 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Software Engineering'], 'year_study' => 1],
            ['name' => 'BA SWE 2 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Software Engineering'], 'year_study' => 2],
            ['name' => 'BA SWE 3 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Software Engineering'], 'year_study' => 3],
            ['name' => 'MSc BAF 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Master of Science in Banking and Finance'], 'year_study' => 1],
            ['name' => 'MSc BUS 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Master of Science in Business Administration'], 'year_study' => 1],
            ['name' => 'MSc CE 1', 'nr_max_student' => 60, 'degree_id' => $degreeIds['Master in Civil Engineering'], 'year_study' => 1],
            ['name' => 'PM BUS 1 A', 'nr_max_student' => 60, 'degree_id' => $degreeIds['PhD in Business Administration'], 'year_study' => 1],
        ];

        DB::table('class_groups')->insert($classGroups);
    }
}