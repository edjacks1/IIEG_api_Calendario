<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table="place";

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'x',
        'y', 'description',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
