

<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">

            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Tambah Data Foto Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="font-medium">Terdapat kesalahan pada form:</p>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="uploadForm" action="{{ route('data-foto.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Upload Photo Section -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 border-b border-red-700">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Upload Foto
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                File Foto <span class="text-red-500">*</span>
                            </label>
                            <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition-colors cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500">
                                            <span>Upload file</span>
                                            <input id="foto"
                                                   name="foto"
                                                   type="file"
                                                   class="sr-only"
                                                   accept="image/*"
                                                   required>
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 15MB</p>
                                </div>
                            </div>
                            <p id="fileStatus" class="mt-2 text-xs text-gray-500"></p>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="hidden mt-4">
                            <div class="relative">
                                <img id="preview" class="rounded-lg shadow-lg max-h-96 mx-auto" alt="Preview">
                                <button type="button"
                                        onclick="clearImage()"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="imageInfo" class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Dasar
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="judul"
                                   name="judul"
                                   value="{{ old('judul') }}"
                                   maxlength="60"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="Masukkan judul foto"
                                   required>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskrp" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea id="deskrp"
                                      name="deskrp"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Masukkan deskripsi foto"
                                      required>{{ old('deskrp') }}</textarea>
                        </div>
                         <div>
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                              <div>
                                <div>
                                   <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2 mt-2">
                                      Penugasan <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                  </label>
                                  <select name="event_id" class="...">
                                    <option value="{{ 0 }}"> - Pilih Penugasan - </option>

                                        @foreach($penugasan as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_event }}</option>
                                        @endforeach
                                  </select>

                                </div>
                            </div>
                          </div>
                         </div>
                         
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                              <div>
                          
                              <div>
                                  <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2 mt-2">
                                      Alat Kelengkapan DPR (AKD) <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                  </label>
                                  <select name="komisi_dpr_id" class="...">
                                    <option value="{{ 0 }}"> - Alat Kelengkapan DPR - </option>
                                        @foreach($komisi as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_komisi }} - {{ $k->bidang }}</option>
                                        @endforeach
                                  </select>
                              </div>

                          </div>
                        </div>
                        <!-- Anggota DPR -->
                          <div>
            <label for="anggota_dpr" class="block text-sm font-medium text-gray-700 mb-2">
                Anggota DPR (Optional)
                <span class="text-xs text-gray-500 font-normal">(Pisahkan dengan koma)</span>
            </label>

            <div class="relative">
                <textarea id="anggota_dpr"
                            name="anggota_dpr"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Ketik minimal 2 huruf untuk melihat suggestion..."
                            autocomplete="off">{{ old('anggota_dpr') }}</textarea>

                <div id="AnggotaDPRSuggestions"
                    class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div id="AnggotaDPRList" class="py-1"></div>

                    <div id="loadingSpinnerAnggota" class="hidden p-3 text-center">
                        <svg class="inline w-5 h-5 text-red-500 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-2 flex items-center justify-between">
                <p class="text-xs text-gray-500">Ketik minimal 2 huruf untuk melihat suggestion</p>
                <span class="text-xs text-gray-600">
                    <span id="anggotaDPRCount" class="font-semibold text-red-600">0</span> keywords
                </span>
            </div>
            </div>
                        <!-- Keywords -->
                        <div>
                            <label for="k_word" class="block text-sm font-medium text-gray-700 mb-2">
                                Keywords
                                <span class="text-xs text-gray-500 font-normal">(Pisahkan dengan koma)</span>
                            </label>
                            <div class="relative">
                                <textarea id="k_word"
                                          name="k_word"
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                          placeholder="Ketik minimal 2 huruf untuk melihat suggestion..."
                                          autocomplete="off">{{ old('k_word') }}</textarea>

                                <div id="keywordSuggestions" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div id="suggestionsList" class="py-1"></div>
                                    <div id="loadingSpinner" class="hidden p-3 text-center">
                                        <svg class="inline w-5 h-5 text-red-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-gray-500">Ketik minimal 2 huruf untuk melihat suggestion</p>
                                <span class="text-xs text-gray-600">
                                    <span id="keywordCount" class="font-semibold text-red-600">0</span> keywords
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photographer & Location Info -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Informasi Fotografer & Lokasi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="perekam" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fotografer <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="perekam" name="perekam"
                                       value="{{ old('perekam', Auth::user()->name) }}" maxlength="60"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>

                            <div>
                                <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subyek <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subyek" name="subyek"
                                       value="{{ old('subyek') }}" maxlength="20"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>

                            <div>
                                <label for="tgl_mm" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tgl_mm" name="tgl_mm"
                                       value="{{ old('tgl_mm', date('Y-m-d')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="mm_lok" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="mm_lok" name="mm_lok"
                                       value="{{ old('mm_lok') }}" maxlength="60"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="Contoh: Bandung, Jawa Barat" required>
                            </div>

                            <div>
                                <label for="kategorisasi_datatempo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kegiatan Lainnya
                                </label>
                                <select name="kategorisasi_datatempo" id="kategorisasi_datatempo"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">-- Kegiatan Lainnya --</option>
                                    @foreach($kategoriFoto as $id => $name)
                                        <option value="{{ $id }}" {{ old('kategorisasi_datatempo') == $id ? 'selected' : '' }}>
                                            {{ ucfirst($name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="konseptor" class="block text-sm font-medium text-gray-700 mb-2">Uploader *readonly</label>
                                <input type="text" id="konseptor" name="konseptor" readonly
                                       value="{{ old('konseptor', Auth::user()->name) }}" maxlength="32"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>


                        </div>

                        @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
                        <div class="flex items-center">
                            <input type="checkbox" id="publish" name="publish" value="1"
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                   {{ old('publish', true) ? 'checked' : '' }}>
                            <label for="publish" class="ml-2 block text-sm text-gray-700">
                                <span class="font-medium">Publikasikan foto ini</span>
                            </label>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-6">
                    <a href="{{ route('data-foto.index') }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-medium transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        <span id="btnText">Simpan Data Foto</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    {{-- Select2 --}}
    <!-- Load jQuery dulu -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d1d5db; /* Tailwind gray-300 */
            border-radius: 0.375rem;   /* rounded-md */
            padding: 0.5rem 0.75rem;   /* py-2 px-3 */
            height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151; /* gray-700 */
            font-size: 0.875rem; /* text-sm */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }
    </style>
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('foto');
        const fileStatus = document.getElementById('fileStatus');
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        let selectedFile = null;


        //select anggota DPR
        $(document).ready(function() {
            $('#anggota_dpr_id').select2({
                placeholder: 'Cari nama anggota DPR...',
                ajax: {
                    url: '{{ route("anggota-dpr.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                width: '100%'
            });
        });

        // Prevent defaults
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        // Visual feedback
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-red-500', 'bg-red-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-red-500', 'bg-red-50');
            });
        });

        // Handle drop
        dropZone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        // Handle click
        dropZone.addEventListener('click', e => {
            if (e.target.id !== 'foto') {
                fileInput.click();
            }
        });

        // Handle file select
        fileInput.addEventListener('change', e => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            console.log('handleFile called with:', file.name, file.size); // Debug

            // Validate
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar!');
                return;
            }

            if (file.size > 50 * 1024 * 1024) {
                alert('Ukuran file maksimal 50MB!');
                return;
            }

            // Store file
            selectedFile = file;

            // Update input dengan DataTransfer
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            console.log('File assigned to input:', fileInput.files[0]?.name); // Debug

            // Update status
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            fileStatus.textContent = `File terpilih: ${file.name} (${sizeMB} MB)`;
            fileStatus.classList.remove('text-gray-500');
            fileStatus.classList.add('text-green-600', 'font-medium');

            // Preview
            previewImage(file);
        }

        function previewImage(file) {
            console.log('previewImage called'); // Debug
            const reader = new FileReader();
            reader.onload = e => {
                console.log('Image loaded, showing preview'); // Debug
                const preview = document.getElementById('imagePreview');
                const img = document.getElementById('preview');
                const info = document.getElementById('imageInfo');

                preview.classList.remove('hidden');
                img.src = e.target.result;

                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                info.innerHTML = `
                    <div class="grid grid-cols-3 gap-2">
                        <div><span class="font-semibold">Nama:</span> ${file.name}</div>
                        <div><span class="font-semibold">Ukuran:</span> ${fileSize} MB</div>
                        <div><span class="font-semibold">Tipe:</span> ${file.type}</div>
                    </div>
                `;
            };
            reader.onerror = error => {
                console.error('FileReader error:', error); // Debug
            };
            reader.readAsDataURL(file);
        }

        function clearImage() {
            selectedFile = null;
            fileInput.value = '';
            fileStatus.textContent = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('preview').src = '';
            document.getElementById('imageInfo').innerHTML = '';
        }

        // Form submit validation
        uploadForm.addEventListener('submit', e => {
            if (!fileInput.files || fileInput.files.length === 0 || !selectedFile) {
                e.preventDefault();
                alert('Silakan pilih file foto terlebih dahulu!');
                return false;
            }

            // Disable button & show loading
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            btnText.textContent = 'Mengupload...';
        });

        // Keyword autocomplete
        const keywordInput = document.getElementById('k_word');
        const suggestionsBox = document.getElementById('keywordSuggestions');
        const suggestionsList = document.getElementById('suggestionsList');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const keywordCount = document.getElementById('keywordCount');
        let searchTimeout;

        function updateKeywordCount() {
            const text = keywordInput.value.trim();
            const count = text ? text.split(',').filter(k => k.trim()).length : 0;
            keywordCount.textContent = count;
        }

        function getCurrentWord() {
            const pos = keywordInput.selectionStart;
            const text = keywordInput.value;
            const before = text.substring(0, pos);
            const lastComma = before.lastIndexOf(',');
            return before.substring(lastComma + 1).trim();
        }

        async function searchKeywords(term) {
            if (term.length < 2) {
                suggestionsBox.classList.add('hidden');
                return;
            }

            loadingSpinner.classList.remove('hidden');
            suggestionsList.innerHTML = '';

            try {
                const response = await fetch(`/api/keywords/search?term=${encodeURIComponent(term)}`);
                if (!response.ok) throw new Error('Search failed');

                const keywords = await response.json();
                loadingSpinner.classList.add('hidden');

                if (keywords.length > 0) {
                    renderSuggestions(keywords);
                    suggestionsBox.classList.remove('hidden');
                } else {
                    suggestionsList.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Tidak ada suggestion</div>';
                    suggestionsBox.classList.remove('hidden');
                }
            } catch (error) {
                loadingSpinner.classList.add('hidden');
                suggestionsBox.classList.add('hidden');
            }
        }

        function renderSuggestions(keywords) {
            suggestionsList.innerHTML = '';
            keywords.forEach(keyword => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-red-50 cursor-pointer text-sm text-gray-700';
                div.textContent = keyword;
                div.addEventListener('click', () => insertKeyword(keyword));
                suggestionsList.appendChild(div);
            });
        }

        function insertKeyword(keyword) {
            const pos = keywordInput.selectionStart;
            const text = keywordInput.value;
            const before = text.substring(0, pos);
            const after = text.substring(pos);
            const lastComma = before.lastIndexOf(',');
            const beforeWord = before.substring(0, lastComma + 1);

            keywordInput.value = beforeWord + (beforeWord.trim() ? ' ' : '') + keyword + ', ' + after;
            suggestionsBox.classList.add('hidden');
            updateKeywordCount();
            keywordInput.focus();
        }

        keywordInput.addEventListener('input', () => {
            updateKeywordCount();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => searchKeywords(getCurrentWord()), 300);
        });

        keywordInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') suggestionsBox.classList.add('hidden');
        });

        document.addEventListener('click', e => {
            if (!keywordInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.add('hidden');
            }
        });

        updateKeywordCount();

        //anggota dpr suggestion
const anggotaInput = document.getElementById('anggota_dpr');
const anggotaBox   = document.getElementById('AnggotaDPRSuggestions');
const anggotaList  = document.getElementById('AnggotaDPRList');
const loadingAnggota = document.getElementById('loadingSpinnerAnggota');
const anggotaCount = document.getElementById('anggotaDPRCount');

let anggotaTimeout;
let abortController = null;

function updateAnggotaCount() {
  const text = anggotaInput.value.trim();
  const count = text ? text.split(',').filter(k => k.trim()).length : 0;
  anggotaCount.textContent = count;
}

function getCurrentAnggotaWord() {
  const pos = anggotaInput.selectionStart;
  const text = anggotaInput.value;
  
  console.log('=== DEBUG getCurrentAnggotaWord ===');
  console.log('Full text:', text);
  console.log('Cursor position:', pos);
  
  // Ambil teks sebelum cursor
  const before = text.substring(0, pos);
  console.log('Text before cursor:', before);
  
  // Cari koma terakhir sebelum cursor
  const lastComma = before.lastIndexOf(',');
  console.log('Last comma index:', lastComma);
  
  // Ambil kata setelah koma terakhir (atau dari awal jika tidak ada koma)
  let currentWord;
  if (lastComma === -1) {
    // Tidak ada koma, ambil dari awal
    currentWord = before.trim();
  } else {
    // Ada koma, ambil setelah koma terakhir
    currentWord = before.substring(lastComma + 1).trim();
  }
  
  console.log('Current word extracted:', `"${currentWord}"`);
  console.log('Current word length:', currentWord.length);
  console.log('=================================');
  
  return currentWord;
}

// Ambil label string dari berbagai bentuk item
function getItemLabel(item) {
  if (typeof item === 'string') return item.trim();
  if (!item || typeof item !== 'object') return '';
  const label = item.nama ?? item.label ?? item.name ?? item.text ?? '';
  return String(label).trim();
}

async function searchAnggota(term) {
  console.log('🔎 searchAnggota called with term:', `"${term}"`, 'length:', term.length);
  
  if (term.length < 2) {
    console.log('❌ Term too short, hiding suggestions');
    anggotaBox.classList.add('hidden');
    return;
  }

  // Cancel previous request
  if (abortController) {
    console.log('⚠️ Aborting previous request');
    abortController.abort();
  }
  abortController = new AbortController();

  loadingAnggota.classList.remove('hidden');
  anggotaList.innerHTML = '';

  const url = `/api/anggota-dpr/search?term=${encodeURIComponent(term)}`;
  console.log('📡 Fetching URL:', url);

  try {
    const response = await fetch(url, {
      headers: { 'Accept': 'application/json' },
      signal: abortController.signal
    });
    
    console.log('📥 Response status:', response.status);
    
    if (!response.ok) throw new Error('Search failed');

    const results = await response.json();
    console.log('✅ Results received:', results);
    console.log('Results count:', results.length);
    
    loadingAnggota.classList.add('hidden');

    const labels = Array.from(new Set(
      (Array.isArray(results) ? results : [])
        .map(getItemLabel)
        .filter(label => label.length > 0)
    ));

    console.log('📋 Processed labels:', labels);

    if (labels.length > 0) {
      renderAnggotaSuggestions(labels);
      anggotaBox.classList.remove('hidden');
    } else {
      anggotaList.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Tidak ada hasil untuk "' + term + '"</div>';
      anggotaBox.classList.remove('hidden');
    }
  } catch (err) {
    if (err.name === 'AbortError') {
      console.log('⚠️ Request aborted');
    } else {
      console.error('❌ Search error:', err);
    }
    loadingAnggota.classList.add('hidden');
    anggotaBox.classList.add('hidden');
  }
}

function renderAnggotaSuggestions(labels) {
  anggotaList.innerHTML = '';
  labels.forEach(label => {
    const div = document.createElement('div');
    div.className = 'px-4 py-2 hover:bg-red-50 cursor-pointer text-sm text-gray-700';
    div.textContent = label;
    div.addEventListener('click', () => insertAnggota(label));
    anggotaList.appendChild(div);
  });
}

function insertAnggota(value) {
  const pos = anggotaInput.selectionStart;
  const text = anggotaInput.value;
  const before = text.substring(0, pos);
  const after  = text.substring(pos);
  const lastComma = before.lastIndexOf(',');

  let beforeWord;
  if (lastComma === -1) {
    beforeWord = '';
  } else {
    beforeWord = before.substring(0, lastComma + 1);
  }

  const needsSpace = beforeWord.trim().length > 0 ? ' ' : '';
  anggotaInput.value = beforeWord + needsSpace + value + ', ' + after;

  // Rapikan spasi/koma ganda
  anggotaInput.value = anggotaInput.value
    .replace(/\s*,\s*/g, ', ')
    .replace(/,\s*,/g, ', ')
    .replace(/\s{2,}/g, ' ')
    .replace(/^,\s*/,'')
    .replace(/,\s*$/,'');

  anggotaBox.classList.add('hidden');
  updateAnggotaCount();
  anggotaInput.focus();
}

anggotaInput.addEventListener('input', () => {
  console.log('⌨️ Input event triggered');
  updateAnggotaCount();
  clearTimeout(anggotaTimeout);
  
  anggotaTimeout = setTimeout(() => {
    const currentWord = getCurrentAnggotaWord();
    searchAnggota(currentWord);
  }, 300);
});

anggotaInput.addEventListener('keydown', e => {
  if (e.key === 'Escape') anggotaBox.classList.add('hidden');
});

document.addEventListener('click', e => {
  if (!anggotaInput.contains(e.target) && !anggotaBox.contains(e.target)) {
    anggotaBox.classList.add('hidden');
  }
});

updateAnggotaCount();

    </script>
    @endpush
</x-app-layout>
