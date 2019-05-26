<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counters extends Model
{
    protected $table = 'counters';
    
    protected $fillable = [
        'department_id',
        'position'
    ];


    public function department(){
        return $this->belongsTo('App\Departments');
    }
}