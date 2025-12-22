<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kategori: ') }} {{ $kategoriFoto->k_name }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->user()?->hasAnyRole(['admin']))
                <a href="{{ route('kategori-foto.edit', $kategoriFoto->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                @endif
                <a href="{{ route('kategori-foto.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Informasi Kategori</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                            <p class="mt-1 text-lg">{{ $kategoriFoto->k_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Foto</label>
                            <p class="mt-1 text-lg">{{ $kategoriFoto->data_foto_count ?? 0 }} foto</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Artikel Draft</label>
                            <p class="mt-1 text-lg">{{ $kategoriFoto->artikel_count ?? 0 }} artikel</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Artikel Editor</label>
                            <p class="mt-1 text-lg">{{ $kategoriFoto->artikel_publish_count ?? 0 }} artikel</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dibuat</label>
                            <p class="mt-1">{{ $kategoriFoto->created_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Terakhir Diupdate</label>
                            <p class="mt-1">{{ $kategoriFoto->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Artikel Draft</h3>
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
                <th class="border p-2">Uploader</th>
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
                    <td class="border p-2">{{ $a->creator->name }}</td>
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
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Artikel Editor</h3>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="border p-2">No</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Tanggal Penugasan</th>
                <th class="border p-2">Tanggal Draft</th>
                <th class="border p-2">Tanggal Edit</th>
                <th class="border p-2">Uploader</th>
                <th class="border p-2">Editor</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($artikelPublish as $i => $a)
                <tr class="hover:bg-gray-50">
                    <td class="border p-2 text-center">{{ $artikels->firstItem() + $i }}</td>
                    <td class="border p-2">{{ $a->judul }}</td>
                    <td class="border p-2">{{ $a?->event?->tanggal->format('d-m-Y H:i:s')??'-' }}</td>
                    <td class="border p-2">{{ $a?->artikelDraft?->tanggal->format('d-m-Y H:i:s')??'-' }}</td>
                    <td class="border p-2">{{ $a?->tanggal->format('d-m-Y H:i:s')??'-' }}</td>
                    <td class="border p-2">{{ $a->artikelDraft?->creator?->name?? '-' }}</td>
                    <td class="border p-2">{{ $a->editor?->name ?? '-' }}</td>
                    <td class="border p-2">
                      @if($a->active==1)
                        <span class="text-green-600">{{ 'Publish @'.$a->updated_at}}</span>
                      @else
                        <span class="text-red-600">{{ 'Not Publish'}}</span>
                      @endif
                    </td>
                    <td class="border p-2 text-center">
                      @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
                        <a href="{{ route('artikel_publish.show', $a->id) }}" class="text-yellow-600 hover:text-yellow-900">View</a> |
                        <a href="{{ route('artikel_publish.edit', $a->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a> |
                        <a href="{{ route('artikel.show', $a->artikel_draft_id) }}" class="text-yellow-600 hover:text-yellow-900">View Draft</a> |
                        <form action="{{ route('artikel_publish.destroy', $a->id) }}" method="POST" class="inline">
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
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Foto</h3>
                     <!-- Photo Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                    @foreach($dataFoto as $foto)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
                            <!-- Image -->
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                <img src="{{ $foto->thumbnail_url }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     alt="{{ $foto->judul }}"
                                     loading="lazy">

                                <!-- Status Badge -->
                                <div class="absolute top-2 left-2">
                                    @if($foto->publish)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>

                                <!-- Stats Overlay -->
                                <div class="absolute bottom-2 right-2 flex gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-black bg-opacity-60 text-white">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ $foto->view }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-black bg-opacity-60 text-white">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        {{ $foto->download }}
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate" title="{{ $foto->judul }}">
                                    {{ Str::limit($foto->judul, 30) }}
                                </h3>

                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit(strip_tags($foto->deskrp), 60) }}
                                </p>

                                <!-- Meta Info -->
                                <div class="space-y-1 text-xs text-gray-500 mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $foto->perekam }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $foto->tgl_mm->format('d M Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                        </svg>
                                        {{ $foto->subyek }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                        </svg>
                                        {{ $foto->formatted_file_size }}
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('data-foto.show', $foto) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-red-600 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition duration-150"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                                    <a href="{{ route('data-foto.edit', $foto) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-red-600 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition duration-150"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    <a href="{{ route('data-foto.download', $foto) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-red-600 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition duration-150"
                                       title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                                    <form action="{{ route('data-foto.destroy', $foto) }}"
                                          method="POST"
                                          class="flex-1"
                                          onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center items-center px-3 py-2 border border-red-600 text-red-600 hover:bg-red-600 hover:text-white rounded-lg text-sm font-medium transition duration-150"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="bg-white rounded-lg shadow-sm px-6 py-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $dataFoto->firstItem() }} - {{ $dataFoto->lastItem() }} dari {{ $dataFoto->total() }} foto
                        </div>
                        <div>
                            {{ $dataFoto->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                </div>
           
            </div>
        </div>
    </div>

</x-app-layout>