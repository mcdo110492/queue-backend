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
        'date_issued'
    ];

    public function logs()
    {
        return $this->hasMany('App\TicketsUsers', 'ticket_id');
    }



    
}