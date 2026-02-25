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

    public function getLocationUrlFormattedAttribute()
    {
        if (!$this->location) {
            return null;
        }

        if (str_starts_with($this->location, 'http')) {
            return $this->location;
        }

        return "https://maps.google.com/?q=" . urlencode($this->location);
    }

    public function getLocationNameFormattedAttribute()
    {
        if (filled($this->location_name)) {
            return $this->location_name;
        }

        return 'KANTOR BAPPERIDA';
    }
}
