<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventOrganizer extends Model
{
    protected $table="event_organizer";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'user_id'
    ];

    public $timestamps=false;

    public function event()
    {
        return $this->belongsTo('App\Models\Event','event_id','id');
    }

    public function organizer()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
