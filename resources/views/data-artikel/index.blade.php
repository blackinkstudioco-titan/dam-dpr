<x-app-layout>
  <x-slot name="header">
      <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              {{ __('Data Artikel') }}
          </h2>
          @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
              <a href="{{ route('artikel.create') }}" class="bg-red-600 text-white px-4 py-2 rounded">+ Tambah Artikel</a>
          @endif
      </div>
  </x-slot>

  <div class="py-12">

      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search -->
                <div class="md:col-span-8">
                <form method="GET" class="flex gap-2">
                  <div class="flex">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari artikel..." class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <button class="bg-red-600 text-white px-4 rounded">Cari</button>
                  </div>
                </form>
              </div>
        </div>
        </div>
      </div>


        <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
              <div class="md:col-span-8">
                @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                <a href="{{ route('artikel.index') }}"
                   class="bg-blue-600 mr-3 text-white px-4 py-2 rounded">
                    Drafter Artikel
                </a>
                @endif
                @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
                <a href="{{ route('artikel_publish.index') }}"
                   class="bg-green-600  text-white px-4 py-2 rounded">
                    Editor Artikel
                </a>
                @endif
              </div>
             
            </div>
        </div>
       </div>



    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="border p-2">No</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Tanggal Penugasan</th>
                <th class="border p-2">Tanggal Artikel</th>
                <th class="border p-2">AKD</th>
                <th class="border p-2">Penulis</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($artikels as $i => $a)
                <tr class="hover:bg-gray-50">
                    <td class="border p-2 text-center">{{ $artikels->firstItem() + $i }}</td>
                    <td class="border p-2">{{ $a->judul }}</td>
                    <td class="border p-2">{{ $a->event?->tanggal?->format('d-m-Y H:i:s') ?? '-' }}</td>
                    <td class="border p-2">{{ $a->tanggal->format('d-m-Y H:i:s') }}</td>
                    <td class="border p-2">{{ $a->komisiDpr?->nama_komisi?? '-' }}</td>
                    <td class="border p-2">{{ $a->penulis }}</td>
                    <td class="border p-2">

                        @if ($a->is_published)
                                      <span class="text-green-600">Editor</span>
                        @else
                                      <span class="text-red-600">Draft</span>
                        @endif

                    </td>
                    <td class="border p-2 text-center">
                        @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                        <a href="{{ route('artikel.show', $a->id) }}" class="text-yellow-600 hover:text-yellow-900">View</a> |
                        <a href="{{ route('artikel.edit', $a->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit / Send to Editor</a> |
                        <form action="{{ route('artikel.destroy', $a->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus artikel ini?')" class="text-red-500">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center p-4">Belum ada artikel</td></tr>
            @endforelse
        </tbody>
    </table>
  </div>
    <div class="mt-4">
        {{ $artikels->links() }}
    </div>
    </div>
</div>
</div>
</x-app-layout>
