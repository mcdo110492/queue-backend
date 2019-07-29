<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $table = "departments";

    protected $fillable = [
        'name',
        'code'
    ];

    public function counter()
    {
        return $this->hasOne('App\Counters', 'department_id');
    }
}