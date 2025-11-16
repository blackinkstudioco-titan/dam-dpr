<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <a href="{{ route('albums.index') }}" class="text-gray-400 hover:text-gray-600">
                        Albums
                    </a>
                    <span class="text-gray-400">/</span>
                    {{ $album->nama_album }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $photos->total() }} foto</p>
            </div>
            <div class="flex gap-2">
                @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                <a href="{{ route('add-photos.index', $album) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Foto
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter Section -->
         
           

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-12 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                        Nama Album: <span class="font-semibold">{{ $album->nama_album }}</span><br>
                        Dibuat oleh: <span class="font-semibold">{{ $album->creator->name }}</span><br>
                        Tanggal dibuat: <span class="font-semibold">{{ $album->created_at->format('d M Y') }}</span><br>
                        @if($album->event)
                        Event terkait: <span class="font-semibold">{{ $album->event->nama_event }}</span><br/>
                        Alat Kelengkapan DPR (AKD) : <span class="font-semibold">{{ $album->komisiDpr->nama_komisi }}</span>
                        @endif 
                    
                        <hr class="mt-4"/>
                        <br/>
                        <div class="flex gap-2">
                        @if (auth()->user()?->hasAnyRole(['admin', 'editor','uploader']))
                        <a href="{{ route('albums.edit', $album->id) }}" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">

                            Edit Album
                        </a>
                        <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Yakin hapus album ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Hapus</button>
                        </form>
                        @endif
                        </div>

                        </div>
                    </div>
                </div>
            </div>
            
    
            <!-- Photos Grid -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @forelse($photos as $photo)
                            <div class="group relative">
                                <!-- Thumbnail -->
                                <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden bg-gray-100">
                                    <img src="{{ asset('storage/' . $photo->thumbnail_foto_url) }}" 
                                         alt="{{ $photo->judul }}"
                                         class="object-cover">
                                    
                                    <!-- Overlay on hover -->
                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 
                                                transition-opacity duration-200 flex items-center justify-center space-x-2">
                                        <a href="{{ route('data-foto.show', $photo->id) }}" 
                                           class="p-2 bg-white/20 hover:bg-white/30 rounded-full">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <button onclick="downloadPhoto('{{ $photo->id }}')"
                                                class="p-2 bg-white/20 hover:bg-white/30 rounded-full">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Photo Info -->
                                <div class="mt-2">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $photo->judul }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $photo->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-gray-500">
                                Tidak ada foto dalam album ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $photos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function downloadPhoto(photoId) {
            window.location.href = `/foto/download/${photoId}`;
        }
    </script>
    @endpush
</x-app-layout>