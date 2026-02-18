<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'user_id',
        'certificate_no',
        'certificate_date',

        // scan sertifikat
        'certificate_scan',
        'certificate_scan_original_name',

        'aspek_1',
        'aspek_2',
        'aspek_3',
        'aspek_4',
        'aspek_5',
        'aspek_6',

        'total',
        'average',
        'grade_letter',

        'assessed_at',
        'apresiasi_khusus',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
        'certificate_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
