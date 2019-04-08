<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketsUsers extends Model
{
    protected $table = 'tickets_users';

    /**
         * Ticket Status
         * 0 - Pending / Back To Queue
         * 1 - Called
         * 2 - Serving
         * 3 - Completed /Finished
         * 4 - Stopped
    */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'status',
        'served_time',
        'complete_time',
    ];

    


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Tickets', 'ticket_id');
    }

    public function scopeByUser($query, $id){

        return $query->where('user_id',$id);
    }


}