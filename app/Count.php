<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Count extends Model
{
    protected $fillable = [
        'calon_id', 'tps_name', 'count', 'photo', 'created_by'
    ];
}
