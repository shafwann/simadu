<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembimbingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_dosen',
        'nidn',
        'email',
        'password',
        'instansi',
        'prodi',
        'fakultas',
        'status',
        'mahasiswa_id'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
