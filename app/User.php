<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable,SoftDeletes;


    protected $table="user";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','second_name','last_name',
        'maternal_surname', 'email',
        'phone','organization_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];


    public function checkPermission($permission){

        $havePer = DB::table('permission')
                ->leftJoin('role_has_permission', 'role_has_permission.permission_id','=','permission.permission_id')
                ->leftJoin('user_has_role','user_has_role.role_id','=','role_has_permission.role_id')
                ->where('user_has_role.user_id','=',$this->id)
                ->where('permission.slug','=',$permission)
                ->first();

        if(!is_null($havePer))
         return true;

         return false;

    }

    public function organization()
    {
        return $this->hasOne('App\Models\Organization','id','organization_id');
    }

    public function roles()
    {
        return $this->hasMany('App\Models\UserRole','user_id','id');
    }

}
