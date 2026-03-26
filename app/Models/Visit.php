<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'path',
        'method',
        'ip',
        'user_agent',
        'user_id'
    ];
}
