<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\EventObserver;
use Carbon\Carbon;

class Event extends Model
{

    use EventObserver;
    protected $table = "event";

    protected $primaryKey = 'id';
 
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
        return $this->hasOne('App\Models\EventType', 'id', 'tag')->withTrashed();
    }

    public function tag()
    {
        return $this->hasOne('App\Models\EventTag', 'id', 'tag')->withTrashed();
    }

    public function creator(){
        return $this->hasOne('App\User','id','created_by')->withTrashed();
    }

    public function place(){
        return $this->hasOne(Place::class,'id','place_id')->withTrashed();
    }

    public function getNotAvaiblePlacesIDs($startDate,$endDate){
        $startDate = Carbon::parse($startDate)->format('Y-m-d H:i:s');
        $endDate   = Carbon::parse($endDate)->format('Y-m-d H:i:s');

        return Event::all()->filter(function($item) use ($startDate,$endDate) {
            if ( 
                    (($item->start_at    >= $startDate ) && ($item->end_at      <= $endDate)) || 
                    (($startDate >= $item->start_at    ) && ($endDate   <= $item->end_at   )) ||
                    (($startDate >= $item->start_at    ) && ($startDate <= $item->end_at   )) || 
                    (($endDate   >= $item->start_at    ) && ($endDate   <= $item->end_at   ))
                ){
              return $item;
            }
        })->pluck('place_id');
    }
}
