<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'name',
        'ticket_number',
        'priority',
        'status',
        'date_issued'
    ];

    public function ticket_user()
    {
        return $this->hasMany('App\TicketsUsers', 'ticket_id');
    }

}
