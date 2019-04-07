<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'priority',
        'status',
        'date_issued'
    ];

    public function logs()
    {
        return $this->hasMany('App\TicketsUsers', 'ticket_id');
    }



    
}