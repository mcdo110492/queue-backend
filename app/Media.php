<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = "media_ads";

    protected $fillable = [
        'media_path',
        'weight',
        'visibility'
    ];
}