<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Laporan;
use App\Models\Assessment;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kategori_instansi',
        'nim',
        'nidn',
        'prodi',
        'instansi',
        'bidang',
        'status',
        'pembimbing_id',
        'fakultas',
        'kegiatan',
        'tanggal_masuk',
        'tanggal_selesai',

        // TAMBAHAN (yang kamu minta)
        'jabatan_pembimbing',
        'nama_pembimbing',
        'nip_pembimbing',

        'face_descriptor',
        'face_image_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'face_descriptor' => 'array',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }

    // semua penilaian
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    // penilaian TERBARU
    public function assessment()
    {
        return $this->hasOne(Assessment::class)->orderByDesc('created_at');
    }

    /**
     * ============================
     * RELASI DOSEN ↔ MAHASISWA
     * Pivot: dosen_mahasiswa
     * ============================
     */

    // ALIAS (biar kompatibel kalau ada kode lama yang memanggil $user->mahasiswa())
    // dosen -> banyak mahasiswa
    public function mahasiswa()
    {
        return $this->belongsToMany(
            User::class,
            'dosen_mahasiswa',
            'dosen_id',
            'mahasiswa_id'
        );
    }

    // dosen → banyak mahasiswa (utama)
    public function mahasiswaBimbingan()
    {
        return $this->belongsToMany(
            User::class,
            'dosen_mahasiswa',
            'dosen_id',
            'mahasiswa_id'
        );
    }

    // mahasiswa → banyak dosen (utama)
    public function dosenPembimbing()
    {
        return $this->belongsToMany(
            User::class,
            'dosen_mahasiswa',
            'mahasiswa_id',
            'dosen_id'
        );
    }

    public function faceResetRequests()
    {
        return $this->hasMany(FaceResetRequest::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\AgencyScope);
    }
}
