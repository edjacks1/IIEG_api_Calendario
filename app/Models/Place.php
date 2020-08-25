<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use SoftDeletes;

    protected $table      = "place";
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'x','y','description','organization_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function organization(){
        return $this->hasOne('App\Models\Organization','id','organization_id')->withTrashed();
    }
}
