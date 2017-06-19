<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];

    public function receive(Worker $worker) {
        $worker->department()->associate($this)->save();
    }
}
