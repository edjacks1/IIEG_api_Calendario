<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table="organization";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'abbreviation',
        'phone', 'email',
        'status','x','y'
    ];

    // public function places()
    // {
    //     return $this->hasMany('App\Models\OrganizationPlace','organization_id','id');
    // }
}
