<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $table = 'tickets';

    /**
         * Ticket Status
         * 0 - Pending / Back To Queue
         * 1 - Called
         * 2 - Serving
         * 3 - Completed /Finished
         * 4 - Stopped
    */
    protected $fillable = [
        'ticket_number',
        'priority',
        'status',
        'date_issued',
        'department_id'
    ];

    public function user()
    {
        return $this->hasMany('App\TicketsUsers', 'ticket_id');
    }

    public function latestUser(){

        return $this->hasOne('App\TicketsUsers', 'ticket_id')->latest();
    }



    
}