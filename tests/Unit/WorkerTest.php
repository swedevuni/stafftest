<?php

namespace Tests\Unit;

use App\Worker;
use App\Department;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkerTest extends TestCase
{
    use DatabaseTransactions;

    private $worker;
    private $department;

    public function setUp() {
        parent::setUp();
        $this->department = factory(Department::class)->create();
        $this->worker = new Worker([
            'name' => 'Иван',
            'surname' => 'Сидоров',
            'patronymic' => 'Петрович',
            'birthdate' => \Carbon\Carbon::createFromDate(1980, 8, 10),
            'email' => 'sidorov@example.com',
            'phone' => '9012345678',
            'work_start' => \Carbon\Carbon::createFromDate(2016, 2, 6),
            'position' => 'Начальник отдела',
            'department_id' => $this->department->id,
        ]);
    }

    /** @test */
    public function worker_has_name()
    {
        $this->assertEquals('Иван', $this->worker->name);
    }

    /** @test */
    public function worker_has_surname()
    {
        $this->assertEquals('Сидоров', $this->worker->surname);
    }

    /** @test */
    public function worker_has_patronymic()
    {
        $this->assertEquals('Петрович', $this->worker->patronymic);
    }

    /** @test */
    public function worker_has_correct_birthdate()
    {
        $this->assertEquals(\Carbon\Carbon::createFromDate(1980, 8, 10)->toDateString(), $this->worker->birthdate->toDateString());
    }

    /** @test */
    public function worker_has_valid_email()
    {
        $this->assertFalse(!filter_var($this->worker->email, FILTER_VALIDATE_EMAIL));
    }

    /** @test */
    public function worker_has_phone_number()
    {
        $this->assertRegExp('/9\d{9}/', $this->worker->phone);
    }

    /** @test */
    public function worker_has_correct_date_of_working_beginning()
    {
        $this->assertEquals(\Carbon\Carbon::createFromDate(2016, 2, 6)->toDateString(), $this->worker->work_start->toDateString());
    }

    /** @test */
    public function worker_can_have_correct_date_of_working_end()
    {
        $this->assertNull($this->worker->work_end);
        $this->worker->work_end = \Carbon\Carbon::now();
        $this->worker->save();
        $this->assertEquals(\Carbon\Carbon::now()->toDateString(), $this->worker->work_end->toDateString());
    }

    /** @test */
    public function worker_has_position()
    {
        $this->assertEquals('Начальник отдела', $this->worker->position);
    }

    /** @test */
    public function worker_belongs_to_department()
    {
        $this->assertEquals($this->department->id, $this->worker->department->id);
    }
}
