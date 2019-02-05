<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UsersCounters extends Model
{
    protected $table = 'users_counters';

    protected $fillable = [
        'user_id',
        'counter_id'
    ];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function counter()
    {
        return $this->belongsTo("App\Counters");
    }

}