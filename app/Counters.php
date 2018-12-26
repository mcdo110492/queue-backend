<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counters extends Model
{
    protected $table = 'counters';
    
    protected $fillable = [
        'counter_name',
        'position'
    ];
}
