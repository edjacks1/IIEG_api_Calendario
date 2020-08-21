<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table="place";

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'x','y','description','status','organization_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function organization(){
        return $this->hasOne('App\Models\Organization','id','organization_id');
    }
}
