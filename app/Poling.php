<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poling extends Model
{
    protected $fillable = [
        'tps_name', 'photo', 'region_name', 'created_by'
    ];
}
