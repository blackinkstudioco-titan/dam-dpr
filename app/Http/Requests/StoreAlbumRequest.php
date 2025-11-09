<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_album' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_album.required' => 'Nama album wajib diisi',
            'nama_album.max' => 'Nama album maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
        ];
    }
}
