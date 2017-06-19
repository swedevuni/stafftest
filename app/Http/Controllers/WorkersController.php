<?php

namespace App\Http\Controllers;

use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class WorkersController extends Controller
{
    protected $worker;

    protected $quantityPerPage = 10;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    public function index(Request $request)
    {
        $department = $request->get('department');
        $working = $request->exists('working') ? $request->get('working') : null;

        $builder = $this->worker->query();
        if ($department) {
            $builder->where('department_id', $department);
        }
        if (isset($working)) {
            $builder->{$working ? 'whereNotNull' : 'whereNull'}('work_end');
        }
        $workers = $builder->paginate($this->quantityPerPage);

        return view('admin.workers.index', [
            'departments' => \App\Department::all(),    
            'workers' => $workers->appends(Input::except('page'))
        ]);
    }
}
