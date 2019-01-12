<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'details',
        'schedule_date',
        'visibility',
        'weight'
    ];
}
