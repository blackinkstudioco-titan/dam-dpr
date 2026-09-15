<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * [FIX] Dropdown*/ 

    protected function prepareForValidation(): void
    {
        $normalize = fn ($value) => in_array($value, [0, '0', ''], true) ? null : $value;
 
        $this->merge([
            'event_id' => $normalize($this->event_id),
            'komisi_dpr_id' => $normalize($this->komisi_dpr_id),
            'kategori_foto_id' => $normalize($this->kategori_foto_id),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_album' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            // nullable + exists: kalau field diisi (bukan null hasil normalisasi
            // di atas), id-nya harus benar-benar ada — supaya id yang salah/usang
            // ditolak dengan pesan validasi 422 yang jelas, bukan error SQL 500.
            'event_id' => 'nullable|integer|exists:events,id',
            'komisi_dpr_id' => 'nullable|integer|exists:komisi_dpr,id',
            'kategori_foto_id' => 'nullable|integer|exists:kategori_foto,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_album.required' => 'Nama album wajib diisi',
            'nama_album.max' => 'Nama album maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'event_id.exists' => 'Penugasan yang dipilih tidak valid.',
            'komisi_dpr_id.exists' => 'Alat Kelengkapan DPR yang dipilih tidak valid.',
            'kategori_foto_id.exists' => 'Kategori foto yang dipilih tidak valid.',
        ];
    }
}
