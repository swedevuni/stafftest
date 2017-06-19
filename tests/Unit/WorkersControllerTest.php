<?php

namespace Tests\Unit;

use App\Worker;
use App\Department;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkersControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $workers;

    protected $workersQuantityPerPage = 10;

    public function setUp() {
        parent::setUp();

        Worker::query()->truncate(); //DatabaseTransactions doesn't always rollback correctly
        $this->workers = factory(Worker::class, 50)->create();
    }

    /** @test */
    public function workers_list_has_departments_data_for_filter()
    {
        $departments = factory(Department::class, 2)->create();

        $response = $this->get('/admin/workers');

        $response->assertStatus(200);
        $response->assertViewHas('departments', function ($receivedDepartments) use ($departments) {
            if (empty($receivedDepartments)) {
                throw new \Exception("No received departments.");
            }
            if ($departmentsAbsentInReceivedCount = $departments->diff($receivedDepartments)->count()) {
                throw new \Exception("Received collection doesn't have {$departmentsAbsentInReceivedCount} expected departments.");
            }
            return true;
        });
    }

    /** @test */
    public function workers_list_is_paginated()
    {
        $expectedWorkers = $this->workers->forPage(3, $this->workersQuantityPerPage);

        $response = $this->get('/admin/workers?page=3');

        $response->assertStatus(200);
        $response->assertViewHas('workers', function ($receivedWorkers) use ($expectedWorkers) {
            if ($expectedWorkersDiffCount = $expectedWorkers->diff($receivedWorkers)->count()) {
                throw new \Exception("Expected {$expectedWorkersDiffCount} more workers from received collection for page.");
            } elseif ($receivedWorkersDiffCount = $receivedWorkers->diff($expectedWorkers)->count()) {
                throw new \Exception("Expected {$receivedWorkersDiffCount} less workers from received collection for page.");
            }
            return true;
        });
    }

    /** @test */
    public function workers_list_is_paginated_for_first_page_by_default()
    {
        $expectedWorkers = $this->workers->forPage(1, $this->workersQuantityPerPage);

        $response = $this->get('/admin/workers');

        $response->assertStatus(200);
        $response->assertViewHas('workers', function ($receivedWorkers) use ($expectedWorkers) {
            if (empty($receivedWorkers)) {
                throw new \Exception("No received workers.");
            }
            if ($expectedWorkersDiffCount = $expectedWorkers->diff($receivedWorkers)->count()) {
                throw new \Exception("Expected {$expectedWorkersDiffCount} more workers from received collection for page.");
            } elseif ($receivedWorkersDiffCount = $receivedWorkers->diff($expectedWorkers)->count()) {
                throw new \Exception("Expected {$receivedWorkersDiffCount} less workers from received collection for page.");
            }
            return true;
        });
    }

    /** @test */
    public function workers_list_can_be_filtered_by_department()
    {
        $expectedWorkers = factory(Worker::class, 5)->create();
        $department = factory(Department::class)->create();
        $department->receive($expectedWorkers)->save();

        $response = $this->get('/admin/workers?department=' . $department->id);

        $response->assertStatus(200);
        $response->assertViewHas('workers', function ($receivedWorkers) use ($expectedWorkers) {
            if ($expectedWorkersDiffCount = $expectedWorkers->diff($receivedWorkers)->count()) {
                throw new \Exception("Expected {$expectedWorkersDiffCount} more workers from received collection for department filter.");
            } elseif ($receivedWorkersDiffCount = $receivedWorkers->diff($expectedWorkers)->count()) {
                throw new \Exception("Expected {$receivedWorkersDiffCount} less workers from received collection for department filter.");
            }
            return true;
        });
    }

    /** @test */
    public function workers_list_can_be_filtered_by_working_status()
    {
        Worker::query()->truncate();
        $expectedWorking = factory(Worker::class, 5)->create(['work_end' => \Carbon\Carbon::now()]);
        $expectedNotWorking = factory(Worker::class, 5)->create(['work_end' => null]);

        $responseWorking = $this->get('/admin/workers?working=1');
        $responseNotWorking = $this->get('/admin/workers?working=0');

        $responseWorking->assertStatus(200);
        $responseNotWorking->assertStatus(200);
        $responseWorking->assertViewHas('workers', function ($receivedWorkers) use ($expectedWorking) {
            if ($workersAbsentInReceivedCount = $expectedWorking->diff($receivedWorkers)->count()) {
                throw new \Exception("Received collection doesn't have {$workersAbsentInReceivedCount} expected workers that are still working.");
            }
            return true;
        });
        $responseNotWorking->assertViewHas('workers', function ($receivedWorkers) use ($expectedNotWorking) {
            if ($workersAbsentInReceivedCount = $expectedNotWorking->diff($receivedWorkers)->count()) {
                throw new \Exception("Received collection doesn't have {$workersAbsentInReceivedCount} expected workers that don't work anymore.");
            }
            return true;
        });
    }

    /** @test */
    public function workers_list_can_be_filtered_by_working_status_and_department_with_pagination_same_time()
    {
        $expectedWorkingFull = factory(Worker::class, 50)->create(['work_end' => \Carbon\Carbon::now()]);
        $expectedNotWorkingFull = factory(Worker::class, 50)->create(['work_end' => null]);
        $expectedWorkingPart = $expectedWorkingFull->reject(function ($worker) {
            return $worker->id % 2 ? $worker : null;
        });
        $expectedNotWorkingPart = $expectedNotWorkingFull->reject(function ($worker) {
            return $worker->id % 2 ? $worker : null;
        });
        $department = factory(Department::class)->create();
        $department->receive($expectedWorkingPart)->save();
        $department->receive($expectedNotWorkingPart)->save();
        $expectedWorking = $expectedWorkingPart->forPage(2, $this->workersQuantityPerPage);
        $expectedNotWorking = $expectedNotWorkingPart->forPage(2, $this->workersQuantityPerPage);

        $responseWorking = $this->get('/admin/workers?department=' . $department->id . '&working=1&page=2');
        $responseNotWorking = $this->get('/admin/workers?department=' . $department->id . '&working=0&page=2');

        $responseWorking->assertStatus(200);
        $responseNotWorking->assertStatus(200);
        $responseWorking->assertViewHas('workers', function ($receivedWorkers) use ($expectedWorking) {
            if ($expectedWorkersDiffCount = $expectedWorking->diff($receivedWorkers)->count()) {
                throw new \Exception("Expected {$expectedWorkersDiffCount} more workers from received collection for filtered page.");
            } elseif ($receivedWorkersDiffCount = $receivedWorkers->diff($expectedWorking)->count()) {
                throw new \Exception("Expected {$receivedWorkersDiffCount} less workers from received collection for filtered page.");
            }
            return true;
        });
        $responseNotWorking->assertViewHas('workers', function ($receivedWorkers) use ($expectedNotWorking) {
            if ($expectedWorkersDiffCount = $expectedNotWorking->diff($receivedWorkers)->count()) {
                throw new \Exception("Expected {$expectedWorkersDiffCount} more workers from received collection for filtered page.");
            } elseif ($receivedWorkersDiffCount = $receivedWorkers->diff($expectedNotWorking)->count()) {
                throw new \Exception("Expected {$receivedWorkersDiffCount} less workers from received collection for filtered page.");
            }
            return true;
        });
    }
}
