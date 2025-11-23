<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Jadwal Publish Foto') }}
            </h2>
            <a href="{{ route('photo-schedule.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('photo-schedule.store') }}" method="POST" id="scheduleForm">
                        @csrf

                        <!-- Pengaturan Jadwal -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-lg mb-4">Pengaturan Jadwal</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="action" class="block text-gray-700 text-sm font-bold mb-2">
                                        Aksi <span class="text-red-500">*</span>
                                    </label>
                                    <select name="action" 
                                            id="action"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('action') border-red-500 @enderror"
                                            required>
                                        <option value="">-- Pilih Aksi --</option>
                                        <option value="publish" {{ old('action') === 'publish' ? 'selected' : '' }}>
                                            📤 Publish (Tampilkan foto)
                                        </option>
                                        <option value="unpublish" {{ old('action') === 'unpublish' ? 'selected' : '' }}>
                                            📥 Unpublish (Sembunyikan foto)
                                        </option>
                                    </select>
                                    @error('action')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="scheduled_at" class="block text-gray-700 text-sm font-bold mb-2">
                                        Waktu Jadwal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" 
                                           name="scheduled_at" 
                                           id="scheduled_at" 
                                           value="{{ old('scheduled_at') }}"
                                           min="{{ now()->format('Y-m-d\TH:i') }}"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('scheduled_at') border-red-500 @enderror"
                                           required>
                                    @error('scheduled_at')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs mt-1">Pilih waktu di masa depan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Foto -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-lg mb-3">Filter Foto</h3>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}"
                                       placeholder="Cari judul atau MM ID..." 
                                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700 flex-1">
                                
                                <select name="kategori" class="shadow border rounded py-2 px-3 text-gray-700">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}" {{ $kategori == $kat->id ? 'selected' : '' }}>
                                            {{ $kat->k_name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <button type="button" 
                                        onclick="filterFotos()"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                                
                                @if($search || $kategori)
                                    <a href="{{ route('photo-schedule.create') }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Selected Photos Preview -->
                        <div id="selectedPreview" class="mb-4 p-4 bg-green-50 rounded-lg hidden">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-lg">Foto Terpilih: <span id="selectedCount">0</span></h3>
                                <button type="button" 
                                        onclick="clearSelection()"
                                        class="text-red-600 hover:text-red-900 text-sm">
                                    Hapus Semua
                                </button>
                            </div>
                            <div id="selectedList" class="text-sm text-gray-600"></div>
                        </div>

                        @error('foto_ids')
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <p>{{ $message }}</p>
                            </div>
                        @enderror

                        <!-- Daftar Foto dengan Checkbox -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-semibold text-lg">Pilih Foto untuk Dijadwalkan</h3>
                                <div class="flex gap-2">
                                    <button type="button" 
                                            onclick="selectAll()"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                        Pilih Semua
                                    </button>
                                    <button type="button" 
                                            onclick="deselectAll()"
                                            class="text-red-600 hover:text-red-900 text-sm">
                                        Batalkan Semua
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                <input type="checkbox" 
                                                       id="selectAllCheckbox"
                                                       onchange="toggleAllCheckboxes(this)"
                                                       class="rounded">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thumbnail</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Foto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($fotos as $foto)
                                            <tr class="hover:bg-gray-50 foto-row" data-foto-id="{{ $foto->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" 
                                                           name="foto_ids[]" 
                                                           value="{{ $foto->id }}"
                                                           class="foto-checkbox rounded"
                                                           onchange="updateSelectedPreview()"
                                                           data-judul="{{ $foto->judul }}"
                                                           data-mmid="{{ $foto->mm_id }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <img src="{{ $foto->thumbnail_url }}" 
                                                         alt="{{ $foto->judul }}" 
                                                         class="h-16 w-16 object-cover rounded">
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="font-medium text-gray-900">{{ $foto->judul }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $foto->mm_id }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $foto->kategori->k_name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($foto->publish)
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Published</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Draft</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                    Tidak ada foto
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $fotos->appends(['search' => $search, 'kategori' => $kategori])->links() }}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="text-sm text-gray-600">
                                <span id="submitCount">0</span> foto akan dijadwalkan
                            </div>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                💾 Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateSelectedPreview() {
            const checkboxes = document.querySelectorAll('.foto-checkbox:checked');
            const count = checkboxes.length;
            const preview = document.getElementById('selectedPreview');
            const selectedList = document.getElementById('selectedList');
            const selectedCount = document.getElementById('selectedCount');
            const submitCount = document.getElementById('submitCount');
            const submitBtn = document.getElementById('submitBtn');

            selectedCount.textContent = count;
            submitCount.textContent = count;

            if (count > 0) {
                preview.classList.remove('hidden');
                submitBtn.disabled = false;
                
                let listHtml = '<ul class="list-disc list-inside">';
                checkboxes.forEach((checkbox, index) => {
                    if (index < 5) { // Tampilkan maksimal 5
                        listHtml += `<li>${checkbox.dataset.judul} (${checkbox.dataset.mmid})</li>`;
                    }
                });
                if (count > 5) {
                    listHtml += `<li class="text-gray-500">... dan ${count - 5} foto lainnya</li>`;
                }
                listHtml += '</ul>';
                
                selectedList.innerHTML = listHtml;
            } else {
                preview.classList.add('hidden');
                submitBtn.disabled = true;
            }
        }

        function toggleAllCheckboxes(source) {
            const checkboxes = document.querySelectorAll('.foto-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
            updateSelectedPreview();
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('.foto-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            document.getElementById('selectAllCheckbox').checked = true;
            updateSelectedPreview();
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('.foto-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateSelectedPreview();
        }

        function clearSelection() {
            deselectAll();
        }

        function filterFotos() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("photo-schedule.create") }}';
            
            const search = document.querySelector('input[name="search"]').value;
            const kategori = document.querySelector('select[name="kategori"]').value;
            
            if (search) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = search;
                form.appendChild(input);
            }
            
            if (kategori) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'kategori';
                input.value = kategori;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedPreview();
        });
    </script>
    @endpush
</x-app-layout>