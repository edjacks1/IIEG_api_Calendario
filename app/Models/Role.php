<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    
    protected $table      = "role";
    protected $primaryKey = 'role_id';
    protected $fillable   = ['name', 'description'];

    public function permissions()
    {
        return $this->hasMany('App\Models\RolePermission','role_id');
    }
}
