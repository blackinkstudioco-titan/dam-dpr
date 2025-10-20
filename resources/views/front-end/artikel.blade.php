<x-frontend-layout>

<!-- Foto Terakhir Section -->
<section class="py-16 bg-white">
      <div class="container mx-auto px-4">
          <div class="flex items-center justify-between mb-8">
              <h2 class="text-3xl font-bold text-gray-800">Data Artikel</h2>
              <a href="{{route('list-artikel')}}" class="text-red-600 hover:text-red-700 font-medium">
                  Lihat Semua →
              </a>
          </div>
          @if($artikels->count() > 0)
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($artikels as $artikel)
                    <a href="{{ route('read-artikel', ['slug' => Str::slug($artikel->judul), 'artikel' => $artikel]) }}" class="block group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">

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

          @endif
          <!-- Pagination -->
          <div class="bg-white rounded-lg shadow-sm px-6 py-4">
              <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                  <div class="text-sm text-gray-600">
                      Menampilkan {{ $artikels->firstItem() }} - {{ $artikels->lastItem() }} dari {{ $artikels->total() }} foto
                  </div>
                  <div>
                      {{ $artikels->withQueryString()->links() }}
                  </div>
              </div>
          </div>
        </div>

</section>
<!-- Artikel Terakhir Section -->
</x-frontend-layout>
