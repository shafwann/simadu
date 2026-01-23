<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'check_in',
        'check_out',
        'status',
        'keterangan',
        'location',
        'location_name',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
