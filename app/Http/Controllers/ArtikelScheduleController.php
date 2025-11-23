<?php

namespace App\Http\Controllers;

use App\Models\ArtikelPublish;
use Illuminate\Http\Request;

class ArtikelScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Tampilkan daftar jadwal artikel
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $schedules = ArtikelPublish::query()
            ->with(['creator'])
            ->where(function($q) {
                $q->whereNotNull('scheduled_publish_at')
                  ->orWhereNotNull('scheduled_unpublish_at');
            })
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('rubrik', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('schedule_status', $status);
            })
            ->orderBy('scheduled_publish_at', 'asc')
            ->paginate(15);

        return view('artikel-schedule.index', compact('schedules', 'search', 'status'));
    }

    /**
     * Form untuk set jadwal artikel (multiple select)
     */
    public function create(Request $request)
    {
        $search = $request->get('search');
        $rubrik = $request->get('rubrik');
        
        $artikels = ArtikelPublish::query()
            ->where('del', 0)
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('penulis', 'like', "%{$search}%");
            })
            ->when($rubrik, function ($query, $rubrik) {
                return $query->where('rubrik', $rubrik);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Ambil list rubrik unique
        $rubriks = ArtikelPublish::select('rubrik')
            ->distinct()
            ->whereNotNull('rubrik')
            ->where('rubrik', '!=', '')
            ->orderBy('rubrik')
            ->pluck('rubrik');

        return view('artikel-schedule.create', compact('artikels', 'search', 'rubrik', 'rubriks'));
    }

    /**
     * Simpan jadwal artikel (multiple)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'artikel_ids' => 'required|array|min:1',
            'artikel_ids.*' => 'exists:artikel_publish,id',
            'action' => 'required|in:publish,unpublish',
            'scheduled_at' => 'required|date|after:now',
        ], [
            'artikel_ids.required' => 'Pilih minimal 1 artikel',
            'artikel_ids.min' => 'Pilih minimal 1 artikel',
            'artikel_ids.*.exists' => 'Artikel tidak valid',
            'action.required' => 'Aksi harus dipilih',
            'action.in' => 'Aksi tidak valid',
            'scheduled_at.required' => 'Waktu jadwal harus diisi',
            'scheduled_at.after' => 'Waktu jadwal harus di masa depan',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Jakarta');

        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['artikel_ids'] as $artikelId) {
            try {
                $artikel = ArtikelPublish::findOrFail($artikelId);

                if ($validated['action'] === 'publish') {
                    $artikel->update([
                        'scheduled_publish_at' => $scheduledAt,
                        'scheduled_unpublish_at' => null,
                        'schedule_status' => 'pending',
                        'active' => 0,
                    ]);
                } else {
                    $artikel->update([
                        'scheduled_unpublish_at' => $scheduledAt,
                        'scheduled_publish_at' => null,
                        'schedule_status' => 'published',
                        'active' => 1,
                    ]);
                }
                
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        $message = "Berhasil menjadwalkan {$successCount} artikel";
        if ($errorCount > 0) {
            $message .= ", gagal {$errorCount} artikel";
        }

        return redirect()->route('artikel-schedule.index')
            ->with('success', $message);
    }

    /**
     * Edit jadwal artikel
     */
    public function edit(ArtikelPublish $artikel)
    {
        return view('artikel-schedule.edit', compact('artikel'));
    }

    /**
     * Update jadwal artikel
     */
    public function update(Request $request, ArtikelPublish $artikel)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Jakarta');

        if ($validated['action'] === 'publish') {
            $artikel->update([
                'scheduled_publish_at' => $scheduledAt,
                'scheduled_unpublish_at' => null,
                'schedule_status' => 'pending',
                'active' => 0,
            ]);
        } else {
            $artikel->update([
                'scheduled_unpublish_at' => $scheduledAt,
                'scheduled_publish_at' => null,
                'schedule_status' => 'published',
                'active' => 1,
            ]);
        }

        return redirect()->route('artikel-schedule.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Batalkan jadwal
     */
    public function cancel(ArtikelPublish $artikel)
    {
        $artikel->update([
            'scheduled_publish_at' => null,
            'scheduled_unpublish_at' => null,
            'schedule_status' => 'cancelled',
        ]);

        return redirect()->route('artikel-schedule.index')
            ->with('success', 'Jadwal berhasil dibatalkan');
    }

    /**
     * Hapus jadwal
     */
    public function destroy(ArtikelPublish $artikel)
    {
        $artikel->update([
            'scheduled_publish_at' => null,
            'scheduled_unpublish_at' => null,
            'schedule_status' => 'cancelled',
        ]);

        return redirect()->route('artikel-schedule.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    /**
     * Batch cancel
     */
    public function batchCancel(Request $request)
    {
        $validated = $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:artikel_publish,id',
        ]);

        $count = ArtikelPublish::whereIn('id', $validated['schedule_ids'])
            ->update([
                'scheduled_publish_at' => null,
                'scheduled_unpublish_at' => null,
                'schedule_status' => 'cancelled',
            ]);

        return redirect()->route('artikel-schedule.index')
            ->with('success', "Berhasil membatalkan {$count} jadwal");
    }
}