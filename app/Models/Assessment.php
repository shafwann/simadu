<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    // Opsional (tidak wajib). Laravel defaultnya sudah pakai "assessments".
    // protected $table = 'assessments';

    protected $fillable = [
        'user_id',
        'certificate_no',
        'certificate_date',

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
