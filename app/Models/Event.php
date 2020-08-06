<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\EventObserver;

class Event extends Model
{

    use EventObserver;
    protected $table = "event";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
        'place_id', 'start_at',
        'end_at', 'created_by',
        'status', 'type', 'tag',
        'resources_check'
    ];

    public function guests()
    {
        return $this->hasMany('App\Models\EventGuest', 'event_id', 'id');
    }

    public function organizers()
    {
        return $this->hasMany('App\Models\EventOrganizer', 'event_id', 'id');
    }

    public function resources()
    {
        return $this->hasMany('App\Models\EventResource', 'event_id', 'id');
    }

    public function type()
    {
        return $this->hasOne('App\Models\EventType', 'id', 'tag');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\EventTag', 'id', 'tag');
    }
}
