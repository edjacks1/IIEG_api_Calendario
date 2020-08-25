<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventResource extends Model
{
    protected $table="event_resource";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'resource_id','isOk'
    ];

    public $timestamps=false;

    public function event()
    {
        return $this->belongsTo('App\Models\Event','event_id','id')->withTrashed();
    }

    public function resource()
    {
        return $this->belongsTo('App\Models\Resource','resource_id','id')->withTrashed();
    }
}
