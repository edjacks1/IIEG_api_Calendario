<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table="user_has_role";

    protected $primaryKey = 'user_has_role_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    public $timestamps=false;
}
