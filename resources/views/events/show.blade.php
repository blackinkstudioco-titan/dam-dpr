<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Event') }}
            </h2>
            <a href="{{ route('events.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $event->nama_event }}</h3>
                        <p class="text-sm text-gray-500">Dibuat oleh: {{ $event->pembuat->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Tanggal & Waktu Event
                        </label>
                        <p class="text-gray-900">
                            {{ $event->tanggal->format('d F Y, H:i') }} WIB
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Deskripsi
                        </label>
                        <p class="text-gray-900">
                            {{ $event->deskripsi ?? 'Tidak ada deskripsi' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Dibuat pada
                        </label>
                        <p class="text-gray-900">
                            {{ $event->created_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <a href="{{ route('events.edit', $event) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Event
                        </a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Yakin ingin menghapus event ini?')">
                                Hapus Event
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>