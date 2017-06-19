<?php

use Illuminate\Database\Seeder;

class DepartmentsAndWorkersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = factory(App\Department::class, 10)->create();
        $workers = factory(App\Worker::class, 500)->create();
    }
}
