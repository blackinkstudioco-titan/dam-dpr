<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('pembuat')->latest()->paginate(10);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        //$validated['pembuat_event'] = Auth::id();
        // Gabungkan tanggal dan waktu menjadi datetime
        $tanggalWaktu = date('Y-m-d H:i:s', strtotime($request->tanggal . ' ' . $request->waktu));

        Event::create([
            'nama_event'    => $request->nama_event,
            'deskripsi'     => $request->deskripsi,
            'tanggal'       => $tanggalWaktu,
            'pembuat_event' => Auth::id(), // ambil ID user yang sedang login
        ]);

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil ditambahkan!');
    }

    public function show(Event $event)
    {
        $event->load('pembuat');
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
        ]);

        $tanggalWaktu = date('Y-m-d H:i:s', strtotime($request->tanggal . ' ' . $request->waktu));

        $event->update([
            'nama_event'    => $request->nama_event,
            'deskripsi'     => $request->deskripsi,
            'tanggal'       => $tanggalWaktu,
        ]);

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}