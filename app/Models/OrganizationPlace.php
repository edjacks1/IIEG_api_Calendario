<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationPlace extends Model
{
    protected $table="organization_place";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id', 'place_id'
    ];

    public $timestamps=false;

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization','organization_id','id');
    }

    public function place()
    {
        return $this->belongsTo('App\Models\Place','place_id','id');
    }
}
