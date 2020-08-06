<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table="role_has_permission";

    protected $primaryKey = 'role_has_permission_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'permission_id',
    ];

    public function permission()
    {
        return $this->belongsTo('App\Models\Permission','permission_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    public $timestamps=false;
}
