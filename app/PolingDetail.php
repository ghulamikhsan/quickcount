<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PolingDetail extends Model
{
    protected $fillable = [
        'poling_id', 'calon_id', 'count',
    ];
}
