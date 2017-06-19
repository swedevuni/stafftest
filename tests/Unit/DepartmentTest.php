<?php

namespace Tests\Unit;

use App\Department;
use App\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepartmentTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function worker_can_be_added_to_department()
    {
        $worker = factory(Worker::class)->create();
        $department = factory(Department::class)->create();

        $department->receive($worker);

        $this->assertEquals($department->id, $worker->department->id);
    }

    /** @test */
    public function multiple_workers_can_be_added_to_department()
    {
        $workers = factory(Worker::class, 10)->create();
        $department = factory(Department::class)->create();

        $department->receive($workers);
        $departmentIdsList = $workers->pluck('department_id');

        $this->assertArraySubset([$department->id], $departmentIdsList, true);
    }
}
