<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    protected $table="event_guest";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'guest_id'
    ];

    public $timestamps=false;

    public function event()
    {
        return $this->belongsTo('App\Models\Event','event_id','id');
    }

    public function guest()
    {
        return $this->belongsTo('App\User','guest_id','id');
    }
}
