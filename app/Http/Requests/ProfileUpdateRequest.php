<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],

            // TAMBAHAN supaya form profile.update boleh menyimpan ini
            'bidang' => ['nullable', 'string', 'max:255'],

            'jabatan_pembimbing' => ['nullable', 'string', 'max:255'],
            'nama_pembimbing' => ['nullable', 'string', 'max:255'],
            'nip_pembimbing' => ['nullable', 'string', 'max:255'],
        ];
    }
}
