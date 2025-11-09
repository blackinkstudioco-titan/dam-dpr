<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ __('Multiple Upload') }}
            </h2>

        </div>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Upload Foto Multiple</h1>
            <p class="text-gray-600 mt-2">Upload banyak foto sekaligus ke dalam album</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <!-- Step 1 -->
                <div class="flex items-center flex-1">
                    <div id="step1-circle" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        1
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-700">Buat Album</p>
                        <p class="text-xs text-gray-500">Isi judul & deskripsi</p>
                    </div>
                </div>

                <div id="step1-line" class="flex-1 h-1 bg-gray-300 mx-4"></div>

                <!-- Step 2 -->
                <div class="flex items-center flex-1">
                    <div id="step2-circle" class="w-10 h-10 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">
                        2
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-400">Upload Foto</p>
                        <p class="text-xs text-gray-400">Drag & drop atau pilih file</p>
                    </div>
                </div>

                <div id="step2-line" class="flex-1 h-1 bg-gray-300 mx-4"></div>

                <!-- Step 3 -->
                <div class="flex items-center flex-1">
                    <div id="step3-circle" class="w-10 h-10 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">
                        3
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-400">Lengkapi Data</p>
                        <p class="text-xs text-gray-400">Metadata tiap foto</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Buat Album -->
        <div id="step1-content" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Step 1: Buat Album Baru</h2>

            <form id="albumForm">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Album <span class="text-red-500">*</span></label>
                    <input type="text"
                           id="nama_album"
                           name="nama_album"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Rapat Paripurna Januari 2024"
                           required>
                    <span class="text-red-500 text-sm hidden" id="error-nama_album"></span>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi Album</label>
                    <textarea id="deskripsi"
                              name="deskripsi"
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsi singkat tentang album ini..."></textarea>
                </div>

                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Wajib diisi
                    </p>
                    <button type="submit"
                            id="btnCreateAlbum"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200">
                        Buat Album & Lanjut
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: Upload Foto -->
        <div id="step2-content" class="bg-white rounded-lg shadow-md p-6 hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Step 2: Upload Foto</h2>

            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    📁 <strong>Album:</strong> <span id="album-name-display"></span>
                </p>
            </div>

            <!-- Dropzone Area -->
            <div id="dropzone"
                 class="border-3 border-dashed border-gray-400 rounded-lg p-12 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition duration-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="mt-4 text-lg text-gray-600">Drag & drop foto di sini</p>
                <p class="text-sm text-gray-500">atau</p>
                <button type="button" id="btnSelectFiles" class="mt-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                    Pilih File
                </button>
                <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, GIF, WEBP | Maksimal 15MB per file</p>
                <input type="file"
                       id="fileInput"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       multiple
                       class="hidden">
            </div>

            <!-- Upload Progress -->
            <div id="uploadProgress" class="mt-6 hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Mengupload...</span>
                    <span id="uploadPercent" class="text-sm font-semibold text-blue-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="uploadBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <span id="uploadedCount">0</span> dari <span id="totalCount">0</span> file
                </p>
            </div>

            <!-- Uploaded Files Preview -->
            <div id="uploadedFiles" class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Files akan muncul di sini -->
            </div>

            <div class="flex justify-between items-center mt-6">
                <button type="button"
                        id="btnBackToStep1"
                        class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50">
                    Kembali
                </button>
                <button type="button"
                        id="btnToStep3"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200 hidden">
                    Lanjut ke Metadata
                </button>
            </div>
        </div>

        <!-- Step 3: Lengkapi Metadata -->
        <div id="step3-content" class="bg-white rounded-lg shadow-md p-6 hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Step 3: Lengkapi Metadata Foto</h2>

            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-800">
                        📁 <strong>Album:</strong> <span id="album-name-display-step3"></span>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        Total <span id="totalPhotos">0</span> foto
                    </p>
                </div>
                <button type="button"
                        id="btnApplyToAll"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    Terapkan ke Semua
                </button>
            </div>

            <div id="metadataContainer" class="space-y-6">
                <!-- Form metadata untuk tiap foto akan muncul di sini -->
            </div>

            <div class="flex justify-between items-center mt-6 pt-6 border-t">
                <button type="button"
                        id="btnBackToStep2"
                        class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50">
                    Kembali
                </button>
                <button type="button"
                        id="btnSaveAll"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200">
                    💾 Simpan Semua Foto
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-lg font-semibold text-gray-800" id="loadingText">Memproses...</p>
            <p class="text-sm text-gray-500 mt-2" id="loadingSubtext">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>



@push('scripts')
<!-- Load jQuery first (synchronously) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- Verify jQuery loaded -->
<script>
    if (typeof jQuery === 'undefined') {
        console.error('❌ jQuery not loaded!');
        alert('jQuery failed to load. Please refresh the page.');
    } else {
        console.log('✅ jQuery loaded, version:', $.fn.jquery);
    }
</script>

<!-- Verify all loaded -->
<script>
    console.log('📄 All scripts loaded');
</script>
<script src="{{ asset('js/bulk-upload.js') }}"></script>
@endpush
</x-app-layout>
