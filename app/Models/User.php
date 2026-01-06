<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Laporan;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim',
        'prodi',
        'instansi',
        'bidang',
        'status',
        'pembimbing_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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

        // dosen → banyak mahasiswa
    public function mahasiswaBimbingan()
    {
        return $this->belongsToMany(
            User::class,
            'dosen_mahasiswa',
            'dosen_id',
            'mahasiswa_id'
        );
    }

    // mahasiswa → banyak dosen
    public function dosenPembimbing()
    {
        return $this->belongsToMany(
            User::class,
            'dosen_mahasiswa',
            'mahasiswa_id',
            'dosen_id'
        );
    }
}
