<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Jadwal Publish Artikel') }}
            </h2>
            <a href="{{ route('artikel-schedule.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Info Artikel -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-3">Informasi Artikel</h3>
                        <div class="flex items-start gap-4">
                            <img src="{{ $artikel->foto_url }}" 
                                 alt="{{ $artikel->judul }}" 
                                 class="h-32 w-32 object-cover rounded-lg shadow">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold text-gray-900">{{ $artikel->judul }}</h4>
                                <p class="text-sm text-gray-500 mt-1">Penulis: {{ $artikel->penulis ?? '-' }}</p>
                                <p class="text-sm text-gray-500">Rubrik: {{ $artikel->rubrik ?? '-' }}</p>
                                
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600">Status Publish:</span>
                                        @if($artikel->active)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Published</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Draft</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600">Jadwal Saat Ini:</span>
                                        @if($artikel->scheduled_publish_at)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                                📤 Publish pada {{ $artikel->scheduled_publish_at->format('d M Y H:i') }} WIB
                                            </span>
                                        @elseif($artikel->scheduled_unpublish_at)
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">
                                                📥 Unpublish pada {{ $artikel->scheduled_unpublish_at->format('d M Y H:i') }} WIB
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('artikel-schedule.update', $artikel->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="action" class="block text-gray-700 text-sm font-bold mb-2">
                                Aksi <span class="text-red-500">*</span>
                            </label>
                            <select name="action" 
                                    id="action"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('action') border-red-500 @enderror"
                                    required>
                                <option value="">-- Pilih Aksi --</option>
                                <option value="publish" {{ ($artikel->scheduled_publish_at || old('action') === 'publish') ? 'selected' : '' }}>
                                    📤 Publish (Tampilkan artikel)
                                </option>
                                <option value="unpublish" {{ ($artikel->scheduled_unpublish_at || old('action') === 'unpublish') ? 'selected' : '' }}>
                                    📥 Unpublish (Sembunyikan artikel)
                                </option>
                            </select>
                            @error('action')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">
                                <strong>Publish:</strong> Artikel akan ditampilkan di halaman publik<br>
                                <strong>Unpublish:</strong> Artikel akan disembunyikan dari halaman publik
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="scheduled_at" class="block text-gray-700 text-sm font-bold mb-2">
                                Waktu Jadwal <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="scheduled_at" 
                                   id="scheduled_at" 
                                   value="{{ old('scheduled_at', ($artikel->scheduled_publish_at ?? $artikel->scheduled_unpublish_at)?->format('Y-m-d\TH:i')) }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('scheduled_at') border-red-500 @enderror"
                                   required>
                            @error('scheduled_at')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">
                                Pilih waktu di masa depan (minimal 1 menit dari sekarang)
                            </p>
                        </div>

                        <!-- Info Waktu Sekarang -->
                        <div class="mb-6 p-3 bg-blue-50 rounded">
                            <p class="text-sm text-blue-800">
                                <strong>ℹ️ Info:</strong> Waktu server saat ini: {{ now()->format('d M Y H:i') }} WIB
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t">
                            <a href="{{ route('artikel-schedule.index') }}" 
                               class="text-gray-600 hover:text-gray-800">
                                ← Kembali ke Daftar
                            </a>
                            <div class="flex gap-2">
                                <button type="button"
                                        onclick="confirmCancel({{ $artikel->id }})"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                                    ❌ Batalkan Jadwal
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                                    💾 Update Jadwal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Batalkan -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Batalkan Jadwal</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin membatalkan jadwal ini?
                    </p>
                    <p class="text-xs text-gray-500 mt-4">
                        Jadwal yang dibatalkan tidak dapat dikembalikan.
                    </p>
                </div>
                <div class="flex gap-2 px-4 py-3">
                    <button onclick="closeModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400">
                        Batal
                    </button>
                    <form action="{{ route('artikel-schedule.cancel', $artikel->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                            Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmCancel(artikelId) {
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('cancelModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>
    @endpush
</x-app-layout>