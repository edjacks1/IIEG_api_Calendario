<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table="role";

    protected $primaryKey = 'role_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];

    public function permissions()
    {
        return $this->hasMany('App\Models\RolePermission','role_id');
    }
}
