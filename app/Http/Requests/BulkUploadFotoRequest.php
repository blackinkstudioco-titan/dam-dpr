<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUploadFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'album_id' => 'required|exists:album_foto,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:15360', // 15MB = 15360KB
        ];
    }

    public function messages(): array
    {
        return [
            'album_id.required' => 'Album harus dipilih',
            'album_id.exists' => 'Album tidak ditemukan',
            'files.required' => 'Minimal 1 file foto harus diupload',
            'files.*.image' => 'File harus berupa gambar',
            'files.*.mimes' => 'Format file harus: jpg, jpeg, png, gif, atau webp',
            'files.*.max' => 'Ukuran file maksimal 15MB',
        ];
    }
}
