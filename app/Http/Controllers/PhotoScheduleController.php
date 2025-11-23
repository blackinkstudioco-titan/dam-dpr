<?php

namespace App\Http\Controllers;

use App\Models\DataFoto;
use Illuminate\Http\Request;

class PhotoScheduleController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Tampilkan daftar jadwal foto
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $schedules = DataFoto::query()
            ->with(['uploader', 'kategori'])
            ->where(function($q) {
                $q->whereNotNull('scheduled_publish_at')
                  ->orWhereNotNull('scheduled_unpublish_at');
            })
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('mm_id', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('schedule_status', $status);
            })
            ->orderBy('scheduled_publish_at', 'desc')
            ->paginate(15);

        return view('photo-schedule.index', compact('schedules', 'search', 'status'));
    }

    /**
     * Form untuk set jadwal foto (multiple select)
     */
    public function create(Request $request)
    {
        $search = $request->get('search');
        $kategori = $request->get('kategori');
        
        $fotos = DataFoto::query()
            ->with(['kategori'])
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('mm_id', 'like', "%{$search}%");
            })
            ->when($kategori, function ($query, $kategori) {
                return $query->where('kategorisasi_datatempo', $kategori);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $kategoris = \App\Models\KategoriFoto::orderBy('k_name')->get();

        return view('photo-schedule.create', compact('fotos', 'search', 'kategori', 'kategoris'));
    }

    /**
     * Simpan jadwal foto (multiple)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'foto_ids' => 'required|array|min:1',
            'foto_ids.*' => 'exists:data_foto,id',
            'action' => 'required|in:publish,unpublish',
            'scheduled_at' => 'required|date|after:now',
        ], [
            'foto_ids.required' => 'Pilih minimal 1 foto',
            'foto_ids.min' => 'Pilih minimal 1 foto',
            'foto_ids.*.exists' => 'Foto tidak valid',
            'action.required' => 'Aksi harus dipilih',
            'action.in' => 'Aksi tidak valid',
            'scheduled_at.required' => 'Waktu jadwal harus diisi',
            'scheduled_at.after' => 'Waktu jadwal harus di masa depan',
        ]);

        // Convert ke timezone yang benar
        $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Jakarta');

        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['foto_ids'] as $fotoId) {
            try {
                $foto = DataFoto::findOrFail($fotoId);

                if ($validated['action'] === 'publish') {
                    $foto->update([
                        'scheduled_publish_at' => $scheduledAt,
                        'scheduled_unpublish_at' => null,
                        'schedule_status' => 'pending',
                        'publish' => 0, // Set ke 0 untuk foto yang akan di-publish
                    ]);
                } else {
                    $foto->update([
                        'scheduled_unpublish_at' => $scheduledAt,
                        'scheduled_publish_at' => null,
                        'schedule_status' => 'published',
                        'publish' => 1, // Set ke 1 untuk foto yang akan di-unpublish
                    ]);
                }
                
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        $message = "Berhasil menjadwalkan {$successCount} foto";
        if ($errorCount > 0) {
            $message .= ", gagal {$errorCount} foto";
        }

        return redirect()->route('photo-schedule.index')
            ->with('success', $message);
    }

    /**
     * Edit jadwal foto
     */
    public function edit(DataFoto $foto)
    {
        return view('photo-schedule.edit', compact('foto'));
    }

    /**
     * Update jadwal foto
     */
    public function update(Request $request, DataFoto $foto)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Jakarta');

        if ($validated['action'] === 'publish') {
            $foto->update([
                'scheduled_publish_at' => $scheduledAt,
                'scheduled_unpublish_at' => null,
                'schedule_status' => 'pending',
                'publish' => 0,
            ]);
        } else {
            $foto->update([
                'scheduled_unpublish_at' => $scheduledAt,
                'scheduled_publish_at' => null,
                'schedule_status' => 'published',
                'publish' => 1,
            ]);
        }

        return redirect()->route('photo-schedule.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Batalkan jadwal
     */
    public function cancel(DataFoto $foto)
    {
        $foto->update([
            'scheduled_publish_at' => null,
            'scheduled_unpublish_at' => null,
            'schedule_status' => 'cancelled',
        ]);

        return redirect()->route('photo-schedule.index')
            ->with('success', 'Jadwal berhasil dibatalkan');
    }

    /**
     * Hapus jadwal
     */
    public function destroy(DataFoto $foto)
    {
        $foto->update([
            'scheduled_publish_at' => null,
            'scheduled_unpublish_at' => null,
            'schedule_status' => 'cancelled',
        ]);

        return redirect()->route('photo-schedule.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    /**
     * Batch cancel - untuk multiple schedule
     */
    public function batchCancel(Request $request)
    {
        $validated = $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:data_foto,id',
        ]);

        $count = DataFoto::whereIn('id', $validated['schedule_ids'])
            ->update([
                'scheduled_publish_at' => null,
                'scheduled_unpublish_at' => null,
                'schedule_status' => 'cancelled',
            ]);

        return redirect()->route('photo-schedule.index')
            ->with('success', "Berhasil membatalkan {$count} jadwal");
    }
}