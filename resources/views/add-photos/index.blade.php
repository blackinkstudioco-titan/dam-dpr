<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <a href="{{ route('albums.show', $album) }}" class="text-gray-400 hover:text-gray-600">
                        {{ $album->nama_album }}
                    </a>
                    <span class="text-gray-400">/</span>
                    Tambah Foto
                </h2>
            </div>
        </div>
    </x-slot>

    <!-- Add this data attribute to a container div -->
    <div id="addPhotosApp" data-album-id="{{ $album->id }}">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Upload Area -->
                <div id="step1-content" class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Upload Foto</h3>
                        <p class="text-sm text-gray-500">Drag and drop foto atau klik untuk memilih file</p>
                    </div>

                    <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <input type="file" id="fileInput" class="hidden" multiple accept="image/*">
                        <div class="space-y-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="text-gray-600">
                                Drag files here or <span class="text-blue-500 hover:text-blue-600 cursor-pointer">browse</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Maximum file size: 15MB
                            </div>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div id="previewArea" class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    </div>
                </div>

                <!-- Metadata Form -->
                <div id="step2-content" class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <div id="metadataContainer" class="space-y-6">
                        <!-- Forms will be inserted here -->
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" 
                                id="btnSaveAll"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                            💾 Simpan Semua Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Modal -->
        <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4">
                <div class="flex items-center justify-center mb-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
                <p class="text-center text-gray-600" id="loadingText">Mengupload foto...</p>
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
    <script>
        // Add this before loading add-photos.js
        window.albumData = {
            id: {{ $album->id }},
            name: "{{ $album->nama_album }}"
        };
    </script>
    <script src="{{ asset('js/add-photos.js') }}"></script>
    @endpush
</x-app-layout>
