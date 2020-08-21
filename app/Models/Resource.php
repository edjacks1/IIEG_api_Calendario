<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table="resource";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner', 'patrimonial_id',
        'type_id', 'name',
        'description', 'remark',
        'status'
    ];

    public function type()
    {
        return $this->hasOne('App\Models\ResourceType','id','type_id');
    }
}
