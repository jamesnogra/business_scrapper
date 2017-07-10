<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'businesses';
    protected $fillable = [
        'place_id', 'name', 'address', 'phone', 'website', 'email', 'opening_hours', 'lat', 'lng',
    ];
}
