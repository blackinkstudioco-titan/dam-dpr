<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          {{ __('Edit Artikel') }}
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
                      Ubah Data Artikel
                  </h3>
              </div>

              <form method="POST" action="{{ route('artikel.update', $artikel->id) }}" enctype="multipart/form-data" class="space-y-4">
                  @csrf
                  @method('PUT')
                  <input type='hidden' name="old_foto" value="{{$artikel->foto}}" />
                  <div class="p-6 space-y-4">
                      <div>
                          <label class="block font-medium">Judul</label>
                          <input type="text" name="judul" value="{{ old('judul', $artikel->judul) }}"
                              class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" required>
                      </div>

                       <div>
                            <div class="flex gap-2">
                                <label class="block font-medium">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal',\Carbon\Carbon::parse($artikel->tanggal)->format('Y-m-d')) }}"
                                    class="border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                <label class="block font-medium">waktu</label>
                                <input type="time" name="waktu" value="{{ old('waktu',\Carbon\Carbon::parse($artikel->tanggal)->format('H:i')) }}"
                                class="border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                    
                            </div>
                        </div>

                      <div>
                          <label class="block font-medium">Foto</label>
                          <input type="file" name="foto"
                              class="border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                      </div>

                      <!-- Preview Foto Lama -->
                      @if($artikel->foto)
                      <div class="mt-4">
                          <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                          <img src="{{ asset('storage/' . $artikel->foto) }}" alt="Foto Lama" class="rounded-lg shadow-md max-h-96" width="180px">
                      </div>
                      @endif

                      <!-- Preview Foto Baru -->
                      <div id="imagePreview" class="hidden mt-4">
                          <div class="relative">
                              <img id="preview" class="rounded-lg shadow-lg max-h-96 mx-auto" alt="Preview" width="180px">
                              <button type="button"
                                      onclick="clearImage()"
                                      class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700 transition">
                                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                  </svg>
                              </button>
                          </div>
                          <div id="imageInfo" class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-600"></div>
                      </div>

                      <div>
                          <label class="block font-medium">Isi Artikel</label>
                          <textarea id="editor" name="isi" rows="10"
                              class="border-gray-300 rounded w-full focus:ring-blue-500 focus:border-blue-500">{{ old('isi', $artikel->isi) }}</textarea>
                      </div>
                        <div>
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                              <div>
                                <div>
                                   <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2 mt-2">
                                      Penugasan <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                  </label>
                                  <select name="event_id" class="...">
                                    <option value="{{ null }}"> - Pilih Penugasan - </option>

                                        @foreach($penugasan as $p)
                                            <option value="{{ $p->id }}" {{ $artikel->event_id == $p->id ? 'selected' : '' }}>{{ $p->nama_event }}</option>
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
                                    <option value="{{ $artikel?->anggotapr?->id ?? ''}}" selected>
                                    {{ $artikel?->anggotaDpr?->nama ?? '-' }}
                                  </option>
                                </div>
                              <div>
                                  <label for="subyek" class="block text-sm font-medium text-gray-700 mb-2 mt-2">
                                      Alat Kelengkapan DPR (AKD) <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                  </label>
                                  <select name="komisi_dpr_id" class="...">
                                    <option value="{{ null }}"> - Alat Kelengkapan DPR - </option>
                                        @foreach($komisi as $k)
                                            <option value="{{ $k->id }}" {{ $artikel->komisi_dpr_id == $k->id ? 'selected' : '' }}>{{ $k->nama_komisi }} - {{ $k->bidang }}</option>
                                        @endforeach
                                  </select>
                              </div>

                          </div>
                        </div>
            <div>
                      <div>
                          <label class="block font-medium">Subyek</label>
                          <input type="text" name="subyek" value="{{ old('subyek',$artikel->subyek) }}"
                              class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" required>
                      </div>
                      <div>
                          <label class="block font-medium">Penulis</label>
                          <input type="text" name="penulis" value="{{ old('penulis',$artikel->penulis) }}"
                              class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" required>
                      </div>
                      <div>
                          <label class="block font-medium">Sumber</label>
                          <input type="text" name="sumber" value="{{ old('sumber',$artikel->sumber) }}"
                              class="w-full border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" required>
                      </div>
                      <div>
                        <label for="k_word" class="block text-sm font-medium text-gray-700 mb-2">
                            Keywords
                            <span class="text-xs text-gray-500 font-normal">(Pisahkan dengan koma)</span>
                        </label>
                        <div class="relative">
                            <textarea id="k_word"
                                      name="keyword"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Ketik minimal 2 huruf untuk melihat suggestion..."
                                      autocomplete="off">{{ old('keyword',$artikel->keyword) }}</textarea>

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

                      <!-- Form Actions -->
                      <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-6">
                          <a href="{{ route('artikel.index') }}"
                             class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-medium transition">
                              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                              </svg>
                              Kembali
                          </a>
                          <button type="submit" id="submitBtn" value="update"
                                  class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                              </svg>
                              <span id="btnText">Perbarui Artikel</span>
                          </button>

                          <button type="submit" name="action" id="submitUpdateBtn" value="kirim_editor"
                                  class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                              </svg>
                              <span id="btnTextEditor">Kirim ke Editor</span>
                          </button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
  </div>

  {{-- Summernote CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

  {{-- jQuery + Summernote JS --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
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
    {{-- Inisialisasi Summernote --}}
    <script>
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
      document.addEventListener('DOMContentLoaded', function() {
            $('#editor').summernote({
                placeholder: 'Tulis isi artikel di sini...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
      // Preview Foto Baru
      document.querySelector('input[name="foto"]').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (!file) return;

          const reader = new FileReader();
          reader.onload = function(event) {
              const preview = document.getElementById('preview');
              const imagePreview = document.getElementById('imagePreview');
              const imageInfo = document.getElementById('imageInfo');

              preview.src = event.target.result;
              imagePreview.classList.remove('hidden');
              imageInfo.innerHTML = `
                  <p><strong>Nama:</strong> ${file.name}</p>
                  <p><strong>Ukuran:</strong> ${(file.size / 1024).toFixed(2)} KB</p>
                  <p><strong>Tipe:</strong> ${file.type}</p>
              `;
          };
          reader.readAsDataURL(file);
      });

      function clearImage() {
          document.querySelector('input[name="foto"]').value = '';
          document.getElementById('imagePreview').classList.add('hidden');
      }
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
</x-app-layout>
