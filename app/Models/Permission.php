<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table="permission";

    protected $primaryKey = 'permission_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description','slug'
    ];

    protected $hidden = [
        'created_at' , 'deleted_at' ,'updated_at'
    ];


    public function rolePermission()
    {
        return $this->belongsTo('App\Models\RolePermission','permission_id')->withTrashed();
    }
}
