<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Jadwal Publish Artikel') }}
            </h2>
            <a href="{{ route('artikel-schedule.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Buat Jadwal Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filter & Search -->
                    <div class="mb-4 flex gap-2">
                        <form action="{{ route('artikel-schedule.index') }}" method="GET" class="flex gap-2 flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="Cari judul atau rubrik..." 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            
                            <select name="status" class="shadow border rounded py-2 px-3 text-gray-700">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Sudah Dipublish</option>
                                <option value="unpublished" {{ $status === 'unpublished' ? 'selected' : '' }}>Sudah Di-unpublish</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cari
                            </button>
                            
                            @if($search || $status)
                                <a href="{{ route('artikel-schedule.index') }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Artikel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($schedules as $index => $artikel)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $schedules->firstItem() + $index }}
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
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ Str::limit($artikel->judul, 50) }}</div>
                                            <div class="text-gray-500 text-xs">Uploader: {{ $artikel?->creator?->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($artikel->scheduled_publish_at)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                                    📤 Publish
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">
                                                    📥 Unpublish
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div>
                                                @if($artikel->scheduled_publish_at)
                                                    <div class="font-medium">{{ $artikel->scheduled_publish_at->format('d M Y') }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $artikel->scheduled_publish_at->format('H:i') }} WIB</div>
                                                @elseif($artikel->scheduled_unpublish_at)
                                                    <div class="font-medium">{{ $artikel->scheduled_unpublish_at->format('d M Y') }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $artikel->scheduled_unpublish_at->format('H:i') }} WIB</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ $artikel->schedule_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $artikel->schedule_status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $artikel->schedule_status === 'unpublished' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $artikel->schedule_status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $artikel->schedule_status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($artikel->schedule_status === 'pending')
                                                <div class="flex gap-2 items-center">
                                                    <a href="{{ route('artikel-schedule.edit', $artikel->id) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">
                                                        ✏️ Edit
                                                    </a>
                                                    
                                                    <button type="button"
                                                            onclick="confirmCancel({{ $artikel->id }}, '{{ $artikel->judul }}')"
                                                            class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs">
                                                        ❌ Batalkan
                                                    </button>
                                                </div>
                                            @elseif($artikel->schedule_status === 'published')
                                                <span class="text-green-600 text-xs">✓ Sudah Dipublish</span>
                                            @elseif($artikel->schedule_status === 'unpublished')
                                                <span class="text-gray-600 text-xs">✓ Sudah Di-unpublish</span>
                                            @elseif($artikel->schedule_status === 'cancelled')
                                                <span class="text-red-600 text-xs">✗ Dibatalkan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada jadwal artikel
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $schedules->appends(['search' => $search, 'status' => $status])->links() }}
                    </div>
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
                        Apakah Anda yakin ingin membatalkan jadwal untuk artikel:
                    </p>
                    <p class="text-sm font-semibold text-gray-900 mt-2" id="cancelArtikelTitle"></p>
                    <p class="text-xs text-gray-500 mt-4">
                        Jadwal yang dibatalkan tidak dapat dikembalikan. Anda perlu membuat jadwal baru jika ingin menjadwalkan artikel ini lagi.
                    </p>
                </div>
                <div class="flex gap-2 px-4 py-3">
                    <button onclick="closeModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none">
                        Batal
                    </button>
                    <form id="cancelForm" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                            Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmCancel(artikelId, judul) {
            const modal = document.getElementById('cancelModal');
            const form = document.getElementById('cancelForm');
            const title = document.getElementById('cancelArtikelTitle');
            
            title.textContent = judul.length > 50 ? judul.substring(0, 50) + '...' : judul;
            form.action = `/artikel-schedule/${artikelId}/cancel`;
            
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