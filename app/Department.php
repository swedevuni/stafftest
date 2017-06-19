<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];

    public function receive($worker) {
        $workers = ($worker instanceof Worker) ? collect([$worker]) : $worker;
        $workers->each(function ($w) {
            $w->department()->associate($this)->save();
        });

        return $this;
    }
}
