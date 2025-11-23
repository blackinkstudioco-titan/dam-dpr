<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Jadwal Publish Artikel') }}
            </h2>
            <a href="{{ route('artikel-schedule.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('artikel-schedule.store') }}" method="POST" id="scheduleForm">
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
                                            📤 Publish (Tampilkan artikel)
                                        </option>
                                        <option value="unpublish" {{ old('action') === 'unpublish' ? 'selected' : '' }}>
                                            📥 Unpublish (Sembunyikan artikel)
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

                        <!-- Filter Artikel -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-lg mb-3">Filter Artikel</h3>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}"
                                       placeholder="Cari judul atau penulis..." 
                                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700 flex-1">
                                
                                <select name="rubrik" class="shadow border rounded py-2 px-3 text-gray-700">
                                    <option value="">Semua Rubrik</option>
                                    @foreach($rubriks as $rub)
                                        <option value="{{ $rub }}" {{ $rubrik == $rub ? 'selected' : '' }}>
                                            {{ $rub }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <button type="button" 
                                        onclick="filterArtikels()"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                                
                                @if($search || $rubrik)
                                    <a href="{{ route('artikel-schedule.create') }}" 
                                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Selected Articles Preview -->
                        <div id="selectedPreview" class="mb-4 p-4 bg-green-50 rounded-lg hidden">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-lg">Artikel Terpilih: <span id="selectedCount">0</span></h3>
                                <button type="button" 
                                        onclick="clearSelection()"
                                        class="text-red-600 hover:text-red-900 text-sm">
                                    Hapus Semua
                                </button>
                            </div>
                            <div id="selectedList" class="text-sm text-gray-600"></div>
                        </div>

                        @error('artikel_ids')
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <p>{{ $message }}</p>
                            </div>
                        @enderror

                        <!-- Daftar Artikel dengan Checkbox -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-semibold text-lg">Pilih Artikel untuk Dijadwalkan</h3>
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
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Artikel</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploader</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Editor</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($artikels as $artikel)
                                            <tr class="hover:bg-gray-50 artikel-row" data-artikel-id="{{ $artikel->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" 
                                                           name="artikel_ids[]" 
                                                           value="{{ $artikel->id }}"
                                                           class="artikel-checkbox rounded"
                                                           onchange="updateSelectedPreview()"
                                                           data-judul="{{ $artikel->judul }}"
                                                           data-penulis="{{ $artikel->penulis }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($artikel->foto!="")
                                                    <img src="{{ $artikel->foto_url }}" 
                                                        alt="{{ $artikel->judul }}" 
                                                        class="h-16 w-16 object-cover rounded">
                                                    @else
                                                         -
                                                     @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="font-medium text-gray-900">{{ Str::limit($artikel->judul, 60) }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $artikel->tanggal ? $artikel->tanggal->format('d M Y') : '-' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $artikel->creator->name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $artikel->editor->name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($artikel->active)
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Published</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Draft</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                    Tidak ada artikel
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $artikels->appends(['search' => $search, 'rubrik' => $rubrik])->links() }}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="text-sm text-gray-600">
                                <span id="submitCount">0</span> artikel akan dijadwalkan
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
            const checkboxes = document.querySelectorAll('.artikel-checkbox:checked');
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
                    if (index < 5) {
                        const judul = checkbox.dataset.judul;
                        const displayJudul = judul.length > 50 ? judul.substring(0, 50) + '...' : judul;
                        listHtml += `<li>${displayJudul}</li>`;
                    }
                });
                if (count > 5) {
                    listHtml += `<li class="text-gray-500">... dan ${count - 5} artikel lainnya</li>`;
                }
                listHtml += '</ul>';
                
                selectedList.innerHTML = listHtml;
            } else {
                preview.classList.add('hidden');
                submitBtn.disabled = true;
            }
        }

        function toggleAllCheckboxes(source) {
            const checkboxes = document.querySelectorAll('.artikel-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
            updateSelectedPreview();
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('.artikel-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            document.getElementById('selectAllCheckbox').checked = true;
            updateSelectedPreview();
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('.artikel-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateSelectedPreview();
        }

        function clearSelection() {
            deselectAll();
        }

        function filterArtikels() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("artikel-schedule.create") }}';
            
            const search = document.querySelector('input[name="search"]').value;
            const rubrik = document.querySelector('select[name="rubrik"]').value;
            
            if (search) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = search;
                form.appendChild(input);
            }
            
            if (rubrik) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rubrik';
                input.value = rubrik;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedPreview();
        });
    </script>
    @endpush
</x-app-layout>