<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Alat Kelengkapan DPR (AKD)') }}
            </h2>
            <a href="{{ route('komisi-dpr.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('komisi-dpr.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nama_komisi" class="block text-gray-700 text-sm font-bold mb-2">
                                Nama AKD <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_komisi" 
                                   id="nama_komisi" 
                                   value="{{ old('nama_komisi') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_komisi') border-red-500 @enderror"
                                   placeholder="Contoh: Komisi I"
                                   autofocus>
                            @error('nama_komisi')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="bidang" class="block text-gray-700 text-sm font-bold mb-2">
                                Bidang
                            </label>
                            <textarea name="bidang" 
                                      id="bidang" 
                                      rows="5"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('bidang') border-red-500 @enderror"
                                      placeholder="Masukkan bidang komisi (contoh: Pertahanan, Keamanan, Luar Negeri)">{{ old('bidang') }}</textarea>
                            @error('bidang')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Opsional: Isi bidang yang dikelola oleh komisi ini</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out">
                                Simpan AKD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>