<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Album Foto') }}
            </h2>
            <a href="{{ url('foto/bulk-upload') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                + Upload Album Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($albums as $album)
                    <a href="{{ route('albums.show', $album) }}" 
                       class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                        <div class="relative pb-[75%]"> <!-- 4:3 aspect ratio -->
                            <img src="{{ asset('storage/' . $album->thumbnail) }}"
                                 alt="{{ $album->nama_album }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                <span class="text-white text-sm">
                                    {{ $album->fotos_count }} foto
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-1">
                                {{ $album->nama_album }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">
                                {{ Str::limit($album->deskripsi, 100) }}
                            </p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>
                                    Dibuat oleh: {{ $album->creator->name }}
                                </span>
                                <span>
                                    {{ $album->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            Belum ada album foto.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $albums->links() }}
            </div>
        </div>
    </div>
</x-app-layout>