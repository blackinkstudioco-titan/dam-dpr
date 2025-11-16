<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Edit Album') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ubah Data Album
                    </h3>
                </div>

                <form method="POST" action="{{ route('albums.update', $album->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block font-medium">Nama Album</label>
                            <input type="text" name="nama_album" value="{{ old('nama_album', $album->nama_album) }}"
                                class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block font-medium">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi', $album->deskripsi) }}</textarea>
                        </div>

                        <div>
                            <label class="block font-medium">Event</label>
                            <select name="event_id" class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Event --</option>
                                @foreach($event as $event)
                                    <option value="{{ $event->id }}" {{ $album->event_id == $event->id ? 'selected' : '' }}>
                                        {{ $event->nama_event }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium">Komisi DPR</label>
                            <select name="komisi_dpr_id" class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Komisi DPR --</option>
                                @foreach($komisi as $komisi)
                                    <option value="{{ $komisi->id }}" {{ $album->komisi_dpr_id == $komisi->id ? 'selected' : '' }}>
                                        {{ $komisi->nama_komisi .'-'. $komisi->bidang  }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('albums.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>