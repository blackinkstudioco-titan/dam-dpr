<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            {{ __('Edit Data Foto') }}
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

            <form id="uploadForm" action="{{ route('data-foto.update', $dataFoto) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Photo & Upload Section -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 border-b border-red-700">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Foto
                        </h3>
                    </div>
                    <div class="p-6">
                        <!-- Current Photo -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
                            <div class="relative inline-block">
                                <img src="{{ $dataFoto->thumbnail_url }}"
                                     alt="{{ $dataFoto->judul }}"
                                     class="rounded-lg shadow-lg max-h-64">
                                <div class="mt-2 text-xs text-gray-500">
                                    <div>File: {{ $dataFoto->f_lok }}</div>
                                    <div>Ukuran: {{ $dataFoto->formatted_file_size }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload New Photo -->
                        <div class="mb-4">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Foto Baru <span class="text-gray-500 text-xs">(Opsional - kosongkan jika tidak ingin mengubah foto)</span>
                            </label>
                            <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition-colors cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500">
                                            <span>Upload file baru</span>
                                            <input id="foto"
                                                   name="foto"
                                                   type="file"
                                                   class="sr-only"
                                                   accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 15MB</p>
                                </div>
                            </div>
                            <p id="fileStatus" class="mt-2 text-xs text-gray-500"></p>
                        </div>

                        <!-- New Photo Preview -->
                        <div id="imagePreview" class="hidden mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Foto Baru</label>
                            <div class="relative inline-block">
                                <img id="preview" class="rounded-lg shadow-lg max-h-64" alt="Preview">
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
                        <!-- MM ID (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">MM ID</label>
                            <input type="text" value="{{ $dataFoto->mm_id }}" readonly
                                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                        </div>

                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="judul" name="judul"
                                   value="{{ old('judul', $dataFoto->judul) }}" maxlength="60"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="Masukkan judul foto" required>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskrp" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea id="deskrp" name="deskrp" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Masukkan deskripsi foto" required>{{ old('deskrp', $dataFoto->deskrp) }}</textarea>
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
                                            <option value="{{ $p->id }}" {{ $dataFoto->event_id == $p->id ? 'selected' : '' }}>{{ $p->nama_event }}</option>
                                        @endforeach
                                  </select>

                                </div>
                            </div>
                          </div>
                         </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                              <div>
                                <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2">Anggota DPR <span class="text-xs text-gray-500 font-normal">(Optional)</span></label>
                                <select id="anggota_dpr_id" name="anggota_dpr_id" class="w-full border-gray-300 rounded"></select>
                                @if($dataFoto->anggotaDpr)
                                  <option value="{{ $dataFoto->anggotaDpr->id }}" selected>
                                    {{ $dataFoto->anggotaDpr->nama }}
                                  </option>
                                @endif
                              </div>
                            <div>
                                <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alat Kelengkapan DPR (AKD) <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <select name="komisi_dpr_id" class="...">
                                  <option value=""> - Pilih Angkat Kelengkapan DPR - </option>
                                      @foreach($komisi as $k)
                                      <option value="{{ $k->id }}" {{ $dataFoto->komisi_dpr_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_komisi }} - {{ $k->bidang }}
                                      </option>
                                      @endforeach
                                </select>
                            </div>

                        </div>
                      </div>
                        <!-- Keywords -->
                        <div>
                            <label for="k_word" class="block text-sm font-medium text-gray-700 mb-2">
                                Keywords
                                <span class="text-xs text-gray-500 font-normal">(Pisahkan dengan koma)</span>
                            </label>
                            <div class="relative">
                                <textarea id="k_word" name="k_word" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                          placeholder="Ketik minimal 2 huruf untuk melihat suggestion..."
                                          autocomplete="off">{{ old('k_word', $dataFoto->k_word) }}</textarea>

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
                            Informasi Foto
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="perekam" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fotografer <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="perekam" name="perekam"
                                       value="{{ old('perekam', $dataFoto->perekam) }}" maxlength="60"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>

                            <div>
                                <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subyek <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subyek" name="subyek"
                                       value="{{ old('subyek', $dataFoto->subyek) }}" maxlength="20"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>

                            <div>
                                <label for="tgl_mm" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tgl_mm" name="tgl_mm"
                                       value="{{ old('tgl_mm', $dataFoto->tgl_mm->format('Y-m-d')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="mm_lok" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="mm_lok" name="mm_lok"
                                       value="{{ old('mm_lok', $dataFoto->mm_lok) }}" maxlength="60"
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
                                        <option value="{{ $id }}" {{ old('kategorisasi_datatempo', $dataFoto->kategorisasi_datatempo) == $id ? 'selected' : '' }}>
                                            {{ ucfirst($name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="konseptor" class="block text-sm font-medium text-gray-700 mb-2">Editor **readonly</label>
                                <input type="text" id="konseptor" name="konseptor" readonly
                                       value="{{ Auth::user()->name }}" maxlength="32"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="depositor" class="block text-sm font-medium text-gray-700 mb-2">Uploader *readonly</label>
                                <input type="text" id="depositor" name="depositor" readonly
                                       value="{{ old('depositor', $dataFoto?->uploader?->name) }}" maxlength="15"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="publish" name="publish" value="1"
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                   {{ old('publish', $dataFoto->publish) ? 'checked' : '' }}>
                            <label for="publish" class="ml-2 block text-sm text-gray-700">
                                <span class="font-medium">Publikasikan foto ini</span>
                            </label>
                        </div>
                    </div>
                </div>



                <!-- Metadata Info (Read-only) -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Sistem</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Tanggal Input:</span>
                                <span class="text-gray-600">{{ $dataFoto->tgl_masuk->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Dibuat Oleh:</span>
                                <span class="text-gray-600">{{ $dataFoto->uploader->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Terakhir Diedit:</span>
                                <span class="text-gray-600">{{ $dataFoto->edit_date ? $dataFoto->edit_date->format('d M Y H:i') : '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Diedit Oleh:</span>
                                <span class="text-gray-600">{{ $dataFoto->editor->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Views:</span>
                                <span class="text-gray-600">{{ $dataFoto->view }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Downloads:</span>
                                <span class="text-gray-600">{{ $dataFoto->download }}</span>
                            </div>
                        </div>
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
                        <span id="btnText">Update Data Foto</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar!');
                return;
            }

            if (file.size > 50 * 1024 * 1024) {
                alert('Ukuran file maksimal 50MB!');
                return;
            }

            selectedFile = file;

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            fileStatus.textContent = `File baru terpilih: ${file.name} (${sizeMB} MB)`;
            fileStatus.classList.remove('text-gray-500');
            fileStatus.classList.add('text-green-600', 'font-medium');

            previewImage(file);
        }

        function previewImage(file) {
            const reader = new FileReader();
            reader.onload = e => {
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

        uploadForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            btnText.textContent = 'Mengupdate...';
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
    </script>
    @endpush
</x-app-layout>
