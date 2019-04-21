<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    protected $table = 'announcements';

    protected $fillable = [
        'message',
        'visibility',
        'weight'
    ];
}