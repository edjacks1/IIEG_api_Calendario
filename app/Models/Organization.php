<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;
    
    protected $table      ="organization";
    protected $primaryKey = 'id';
    protected $fillable   = ['name', 'abbreviation','phone', 'email','x','y'];

}
