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
    ];

    protected $casts = [
        'is_validated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
