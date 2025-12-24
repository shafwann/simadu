<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar Laravel mengizinkan pengisian data
    protected $fillable = [
        'user_id',
        'tanggal',
        'kegiatan',
        'catatan_pembimbing',
        'is_validated'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
