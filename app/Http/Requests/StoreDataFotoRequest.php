<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataFotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or add your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string'],
            'deskrp' => ['required', 'string'],
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:51200'], // max 50MB
            'k_word' => ['nullable', 'string'],
            'perekam' => ['required', 'string'],
            'subyek' => ['required', 'string'],
            'mm_lok' => ['required', 'string'],
            'tgl_mm' => ['required', 'date'],
            'konseptor' => ['nullable', 'string'],
            'depositor' => ['nullable', 'string'],
            'judul_en' => ['nullable', 'string'],
            'deskrp_en' => ['nullable', 'string'],
            'kategorisasi_datatempo' => ['nullable', 'string'],
            'publish' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul foto',
            'deskrp' => 'deskripsi',
            'foto' => 'file foto',
            'k_word' => 'kata kunci',
            'perekam' => 'nama perekam',
            'subyek' => 'subjek',
            'mm_lok' => 'lokasi multimedia',
            'tgl_mm' => 'tanggal multimedia',
            'konseptor' => 'nama konseptor',
            'depositor' => 'nama depositor',
            'judul_en' => 'judul (English)',
            'deskrp_en' => 'deskripsi (English)',
            'kategorisasi_datatempo' => 'kategori',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul foto wajib diisi.',
            'deskrp.required' => 'Deskripsi wajib diisi.',
            'foto.required' => 'File foto wajib diupload.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran foto maksimal 50MB.',
            'perekam.required' => 'Nama perekam wajib diisi.',
            'subyek.required' => 'Subjek wajib diisi.',
            'mm_lok.required' => 'Lokasi multimedia wajib diisi.',
            'tgl_mm.required' => 'Tanggal multimedia wajib diisi.',
            'tgl_mm.date' => 'Format tanggal tidak valid.',
        ];
    }
}
