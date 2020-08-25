<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;

    protected $table      = "event_type";
    protected $primaryKey = 'id';
    protected $fillable   = ['name', 'description'];
}
