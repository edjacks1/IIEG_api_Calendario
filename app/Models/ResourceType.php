<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    protected $table="resource_type";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description','status'
    ];
}
