<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AgencyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::hasUser()) {
            $user = Auth::user();

            // 1. Master Admin melihat semua (bebas)
            if ($user->role === 'master') {
                return;
            }

            // 2. Admin Instansi / Mahasiswa hanya melihat data yang "kategori_instansi"-nya SAMA
            if (!empty($user->kategori_instansi)) {
                $builder->where('kategori_instansi', $user->kategori_instansi);
            }
        }
    }
}
