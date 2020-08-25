<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceType extends Model
{

    use SoftDeletes;

    protected $table      = "resource_type";
    protected $primaryKey = 'id';
    protected $fillable   = ['name', 'description','status'];
}
