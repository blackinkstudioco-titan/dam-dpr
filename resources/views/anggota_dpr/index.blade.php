<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ __('Data Anggota DPR') }}
            </h2>

        </div>
    </x-slot>
<div class="p-6">
    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold">Daftar Anggota DPR</h1>
        <a href="{{ route('anggota-dpr.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">+ Tambah</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('success') }}</div>
    @endif

    <table class="min-w-full bg-white border border-gray-300 rounded-md">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-3 border">Nama</th>
                <th class="py-2 px-3 border">Partai</th>
                <th class="py-2 px-3 border">Fraksi</th>
                <th class="py-2 px-3 border">Komisi</th>
                <th class="py-2 px-3 border">Dapil</th>
                <th class="py-2 px-3 border">Jabatan</th>
                <th class="py-2 px-3 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($anggota as $a)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 px-3">{{ $a->nama }}</td>
                <td class="py-2 px-3">{{ $a->partai }}</td>
                <td class="py-2 px-3">{{ $a->fraksi->nama_fraksi ?? '-' }}</td>
                <td class="py-2 px-3">{{ $a->komisi->nama_komisi ?? '-' }}</td>
                <td class="py-2 px-3">{{ $a->dapil ?? '-' }}</td>
                <td class="py-2 px-3">{{ $a->jabatan ?? '-' }}</td>
                <td class="py-2 px-3 flex gap-2">
                    <a href="{{ route('anggota-dpr.edit', $a->id) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('anggota-dpr.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $anggota->links() }}</div>
</div>
</x-app-layout>
