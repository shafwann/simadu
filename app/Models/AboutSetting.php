<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSetting extends Model
{
    protected $table = 'about_settings';

    protected $fillable = [
        'nama_instansi',
        'kepala_instansi',
        'nip_kepala_instansi',
        'jam_operasional',

        // Lokasi kantor (Absensi GPS)
        'office_latitude',
        'office_longitude',
        'radius_meter',

        // Jam check-in per hari
        'senin_mulai',
        'senin_selesai',

        'selasa_mulai',
        'selasa_selesai',

        'rabu_mulai',
        'rabu_selesai',

        'kamis_mulai',
        'kamis_selesai',

        'jumat_mulai',
        'jumat_selesai',

        // Jam check-out per hari
        'senin_checkout_mulai',
        'senin_checkout_selesai',

        'selasa_checkout_mulai',
        'selasa_checkout_selesai',

        'rabu_checkout_mulai',
        'rabu_checkout_selesai',

        'kamis_checkout_mulai',
        'kamis_checkout_selesai',

        'jumat_checkout_mulai',
        'jumat_checkout_selesai',
    ];
}
