<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table="event_type";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];
}
