<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{

    use SoftDeletes;

    protected $table      = "resource";
    protected $primaryKey = 'id';

    protected $fillable = [
        'owner', 
        'patrimonial_id',
        'type_id', 
        'name',
        'description', 
        'remark'
    ];

    public function type(){
        return $this->hasOne('App\Models\ResourceType','id','type_id')->withTrashed();
    }
}
