<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTag extends Model
{
    protected $table="event_tag";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color','status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
