<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'nama_file',
        'is_validated',
        'is_rejected',
        'rejected_at',
        'rejected_note',
    ];

    protected $casts = [
        'is_validated' => 'boolean',
        'is_rejected' => 'boolean',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
