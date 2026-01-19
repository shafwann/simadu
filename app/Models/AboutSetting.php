<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSetting extends Model
{
    protected $table = 'about_settings';

    protected $fillable = [
        'kategori_instansi',
        'nama_instansi',
        'kepala_instansi',
        'nip_kepala_instansi',
        'jam_operasional',

        // Lokasi kantor (Absensi GPS)
        'office_latitude',
        'office_longitude',
        'radius_meter',
        'office_name',

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


        // ✅ Tambahkan Branding Baru
        'app_name',
        'agency_name',
        'app_description',
        'logo_path',

        // ✅ TAMBAHAN: simpan opsi bidang/divisi dari menu About (format JSON)
        'bidang_options',


        // ✅ TAMBAHAN: Footer & Kontak
        'office_address',
        'office_phone',
        'office_email',
        'social_instagram',
        'social_facebook',
        'google_maps_embed',
        // 'whatsapp_number',
        // 'link_website',
        // 'link_ppid',
        // 'link_opendata',
        'social_youtube',
        'social_x',
        'custom_links',
    ];

    /**
     * ✅ TAMBAHAN: Ambil opsi Bidang/Divisi dari DB (fallback ke default jika kosong)
     */
    public static function bidangOptions($instansi = null): array
    {
        // Jika instansi tidak disebut, coba ambil dari user login
        if (!$instansi && \Illuminate\Support\Facades\Auth::check()) {
            $instansi = \Illuminate\Support\Facades\Auth::user()->kategori_instansi;
        }

        // Default BAPPERIDA jika null
        $kategori = $instansi ?? 'BAPPERIDA';

        $about = self::where('kategori_instansi', $kategori)->first();

        $arr = null;
        if ($about && $about->bidang_options) {
            $arr = json_decode($about->bidang_options, true);
        }

        if (is_array($arr) && count($arr) > 0) {
            return $arr;
        }

        // Default Fallback
        return [
            'Ekonomi', 'Sekretariat', 'Sosial & Budaya',
            'Fisik & Prasarana', 'Penelitian, Pengembangan & Penyusunan Program'
        ];
    }
}
