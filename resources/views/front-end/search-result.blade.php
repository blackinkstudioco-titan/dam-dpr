<x-frontend-layout>
    <!-- Hasil Pencarian Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Sidebar (30%) -->
                <aside class="w-full lg:w-[30%]">
                  <form action="{{ route('search') }}" method="GET" class="relative">
                    <div class="bg-gray-50 rounded-lg shadow-md p-6 sticky top-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Filter Pencarian</h3>
                        <input type="hidden" name="cat" value="{{request('cat')}}">
                        <!-- Info Keyword -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2">Kata Kunci:</p>
                            <input
                                type="text"
                                name="q"
                                value="{{request('q')}}"
                                placeholder="Searching data digital..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <!-- Filter Tambahan (Opsional) -->
                        <div class="space-y-4 mb-4 pb-4">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Urutkan</h4>
                                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Terbaru</option>
                                    <option>Terlama</option>
                                    <option>Judul A-Z</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-6 pb-6 border-b border-gray-200">
                          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                    Cari
                          </button>
                        </div>
                    </div>
                  </form>
                </aside>

                <!-- Main Content (70%) -->
                <main class="w-full lg:w-[70%]">
                    <!-- Header -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Hasil Pencarian</h2>
                    </div>
                    <div class="mb-8 bg-gray-200 p-3 text-gray-400 flex gap-4">
                    <x-nav-link
                            :href="route('search', ['q' => request('q'), 'cat' => 'artikel'])"
                            :active="request()->routeIs('artikel')"
                            class="text-red-700 hover:text-red-700 data-[active=true]:text-red-600 font-semibold transition"
                        >
                            {{ __('Data Artikel') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('search', ['q' => request('q'), 'cat' => 'foto'])"
                            :active="request()->routeIs('foto')"
                            class="text-red-700 hover:text-red-700 data-[active=true]:text-red-600 font-semibold transition"
                        >
                            {{ __('Data Foto') }}
                        </x-nav-link>
                    </div>

                    @if($dataFoto->count() > 0)
                        <!-- Photo Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            @foreach($dataFoto as $foto)
                                <!-- Foto item -->
                                <a href="{{ route('foto-detail', ['slug' => Str::slug($foto->judul), 'dataFoto' => $foto]) }}"
                                   class="block group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                                    <img
                                        src="{{ $foto->thumbnail_url }}"
                                        alt="{{ $foto->judul }}"
                                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                                        <div class="absolute bottom-0 left-0 right-0 p-4">
                                            <h3 class="text-white font-semibold text-lg">{{ $foto->judul }}</h3>
                                            <p class="text-gray-200 text-sm">{{ $foto->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </a>
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
                    @elseif($dataArtikel->count() > 0)
                        <!-- Article Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            @foreach($dataArtikel as $artikel)
                                <!-- Artikel item -->
                                <a href="{{ route('read-artikel', ['slug' => Str::slug($artikel->judul), 'artikel_publish' => $artikel]) }}" class="block group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">

                                    <div class="border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4 flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-semibold text-lg text-gray-800 mb-2 line-clamp-2">
                                                {{ $artikel->judul }}
                                            </h3>
                                            <p class="text-sm text-gray-500 mb-1">
                                                {{ $artikel->tanggal ? $artikel->tanggal->format('d M Y') : '-' }}
                                            </p>
                                            <p class="text-sm text-gray-600 line-clamp-3">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($artikel->isi), 120) }}
                                            </p>
                                        </div>

                                        <div class="mt-4 flex justify-between items-center text-sm">
                                            <span class="text-gray-500 italic">
                                                {{ $artikel->penulis ?? 'Anonim' }}
                                            </span>
                                        </div>
                                    </div>
                                  </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white rounded-lg shadow-sm px-6 py-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-600">
                                    Menampilkan {{ $dataArtikel->firstItem() }} - {{ $dataArtikel->lastItem() }} dari {{ $dataArtikel->total() }} artikel
                                </div>
                                <div>
                                    {{ $dataArtikel->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Results -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada hasil</h3>
                            <p class="mt-1 text-sm text-gray-500">Coba kata kunci lain</p>
                        </div>
                    @endif
                </main>

            </div>
        </div>
    </section>
</x-frontend-layout>
