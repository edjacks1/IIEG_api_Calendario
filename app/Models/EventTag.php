<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTag extends Model
{

    use SoftDeletes;

    protected $table      = "event_tag";
    protected $primaryKey = 'id';
    protected $fillable   = ['name', 'color','status'];
    protected $hidden     = ['created_at', 'updated_at'];
}
