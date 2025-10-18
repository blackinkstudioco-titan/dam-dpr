<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Foto') }}
            </h2>
            <a href="{{ route('data-foto.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Row 1: Main Foto & Deskripsi (2 Kolom) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Col 1: Main Foto --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Foto Utama
                        </h3>

                        {{-- Image Container --}}
                        <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                            <img
                                src="{{ $dataFoto->foto_url }}"
                                alt="{{ $dataFoto->judul }}"
                                class="w-full h-auto max-h-[500px] object-contain"
                                id="mainImage"
                            >

                            {{-- Image Overlay Actions --}}
                            <div class="absolute top-4 right-4 flex space-x-2">
                                <button
                                    onclick="toggleFullscreen()"
                                    class="bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-2 rounded-lg transition"
                                    title="Fullscreen"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Publish Status Badge --}}
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $dataFoto->publish ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                    {{ $dataFoto->publish ? 'Published' : 'Unpublished' }}
                                </span>
                            </div>
                        </div>

                        {{-- Image Stats --}}
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span id="viewCount">{{ number_format($dataFoto->view) }}</span> views
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ number_format($dataFoto->download) }} downloads
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $dataFoto->formatted_file_size }}
                                </span>
                            </div>
                        </div>

                        {{-- Download Button --}}
                        <div class="mt-4">
                            <a
                                href="{{ route('data-foto.download', $dataFoto) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download Foto
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Col 2: Deskripsi Image --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Deskripsi Foto
                        </h3>

                        <div>
                            <h1 class="text-2xl font-bold mb-2">{{ $dataFoto->judul }}</h1>

                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h4>
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $dataFoto->deskrp ?? 'Tidak ada deskripsi.' }}</p>
                                </div>
                            </div>
                            @if($dataFoto->subyek)
                                <div class="border-b pb-3 pt-3">
                                    <dt class="text-sm font-medium text-gray-500">Subyek</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->subyek }}</dd>
                                </div>
                            @endif
                            @if($dataFoto->tgl_mm)
                                <div class="border-b pb-3 pt-3">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Foto</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->tgl_mm->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if($dataFoto->mm_lok)
                                <div class="border-b pb-3 pt-3">
                                    <dt class="text-sm font-medium text-gray-500">Lokasi Foto</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $dataFoto->mm_lok }}
                                        </span>
                                    </dd>
                                </div>
                            @endif
                            {{-- Keywords --}}
                            @if($dataFoto->k_word)
                                <div class="mt-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Keywords</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(explode(',', $dataFoto->k_word) as $keyword)
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                                {{ trim($keyword) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Detail Informasi & Meta Data (2 Kolom) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Col 1: Detail Informasi Foto --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detail Informasi Foto
                        </h3>

                        <dl class="space-y-3">
                            <div class="border-b pb-3">
                                <dt class="text-sm font-medium text-gray-500">MM ID</dt>
                                <dd class="text-sm text-gray-900 font-mono mt-1">{{ $dataFoto->mm_id }}</dd>
                            </div>

                            @if($dataFoto->kategori)
                                <div class="border-b pb-3">
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $dataFoto->kategori->k_name }}
                                        </span>
                                    </dd>
                                </div>
                            @endif



                            <div class="border-b pb-3">
                                <dt class="text-sm font-medium text-gray-500">Tanggal Masuk</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->tgl_masuk->format('d F Y') }}</dd>
                            </div>





                            @if($dataFoto->perekam)
                                <div class="border-b pb-3">
                                    <dt class="text-sm font-medium text-gray-500">Perekam</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->perekam }}</dd>
                                </div>
                            @endif

                            @if($dataFoto->konseptor)
                                <div class="border-b pb-3">
                                    <dt class="text-sm font-medium text-gray-500">Konseptor</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->konseptor }}</dd>
                                </div>
                            @endif

                            @if($dataFoto->depositor)
                                <div class="border-b pb-3">
                                    <dt class="text-sm font-medium text-gray-500">Depositor</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->depositor }}</dd>
                                </div>
                            @endif

                            <div class="border-b pb-3">
                                <dt class="text-sm font-medium text-gray-500">Dibuat oleh</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $dataFoto->k_name }}</dd>
                            </div>

                            @if($dataFoto->edit_by)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Terakhir diubah</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        {{ $dataFoto->edit_by }}
                                        <span class="block text-xs text-gray-500 mt-1">{{ $dataFoto->edit_date->format('d M Y H:i') }}</span>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Col 2: Meta Data Foto (EXIF) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Meta Data Foto (EXIF)
                        </h3>

                        @if($dataFoto->formatted_meta_data && count($dataFoto->formatted_meta_data) > 0)
                            <dl class="space-y-3">
                                @foreach($dataFoto->formatted_meta_data as $label => $value)
                                    <div class="border-b pb-3 last:border-b-0">
                                        <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                                        <dd class="text-sm text-gray-900 mt-1 font-mono">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Tidak ada data EXIF tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Row 3: Aksi (Full Width) --}}
            @auth
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Aksi
                        </h3>

                        <div class="flex flex-wrap gap-3">
                            <a
                                href="{{ route('data-foto.edit', $dataFoto) }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Foto
                            </a>

                            <button
                                onclick="togglePublishStatus()"
                                id="publishBtn"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span id="publishBtnText">{{ $dataFoto->publish ? 'Unpublish' : 'Publish' }}</span>
                            </button>

                            <button
                                onclick="confirmDelete()"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Foto
                            </button>

                            <form
                                id="deleteForm"
                                action="{{ route('data-foto.destroy', $dataFoto) }}"
                                method="POST"
                                class="hidden"
                            >
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    @push('scripts')
    <script>
    // Toggle Fullscreen
    function toggleFullscreen() {
        const img = document.getElementById('mainImage');
        if (!document.fullscreenElement) {
            img.requestFullscreen().catch(err => {
                console.error('Error attempting to enable fullscreen:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // Toggle Publish Status
    function togglePublishStatus() {
        if (!confirm('Apakah Anda yakin ingin mengubah status publish foto ini?')) {
            return;
        }

        fetch('{{ route('data-foto.toggle-publish', $dataFoto) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status publish.');
        });
    }

    // Confirm Delete
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteForm').submit();
        }
    }
    </script>
    @endpush
</x-app-layout>
