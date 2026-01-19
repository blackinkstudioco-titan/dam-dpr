<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Event') }}
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
                    <form action="{{ route('events.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nama_event" class="block text-gray-700 text-sm font-bold mb-2">
                                Nama Event <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_event" id="nama_event" value="{{ old('nama_event') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_event') border-red-500 @enderror"
                                placeholder="Masukkan nama event">
                            @error('nama_event')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="deskripsi" id="deskripsi" rows="4"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('deskripsi') border-red-500 @enderror"
                                placeholder="Masukkan deskripsi event">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                              <div class="flex gap-2">
                                <label class="block font-medium">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                                    class="border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                <label class="block font-medium">waktu</label>
                                <input type="time" name="waktu" value="{{ old('waktu') }}"
                                    class="border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            @error('tanggal')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out">
                                Simpan Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#tanggal", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            minDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
    </script>
    @endpush
</x-app-layout>