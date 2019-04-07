<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketsUsers extends Model
{
    protected $table = 'tickets_users';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'status',
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