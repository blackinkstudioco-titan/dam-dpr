<x-frontend-layout>

<!-- Foto Terakhir Section -->
<section class="py-16 bg-white">
      <div class="container mx-auto px-4">
          <div class="flex items-center justify-between mb-8">
              <h2 class="text-3xl font-bold text-gray-800">Data Foto</h2>
              <a href="{{route('foto')}}" class="text-red-600 hover:text-red-700 font-medium">
                  Lihat Semua →
              </a>
          </div>
          @if($dataFoto->count() > 0)
              <!-- Photo Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                  @foreach($dataFoto as $foto)
                    <!-- Foto item -->
                    <a href="{{ route('foto-detail', ['slug' => Str::slug($foto->judul), 'dataFoto' => $foto]) }}" class="block group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                          <img
                              src="{{ $foto->thumbnail_url }}"
                              alt="{{ $foto->judul }}"
                              class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                          >
                          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                              <div class="absolute bottom-0 left-0 right-0 p-4">
                                  <h3 class="text-white font-semibold text-lg">{{ $foto->judul }}</h3>
                                  <p class="text-gray-200 text-sm">{{ $foto->tgl_mm->format('d M Y') }}</p>
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
                      Menampilkan {{ $dataFoto->firstItem() }} - {{ $dataFoto->lastItem() }} dari {{ $dataFoto->total() }} foto
                  </div>
                  <div>
                      {{ $dataFoto->withQueryString()->links() }}
                  </div>
              </div>
          </div>
        </div>

</section>
<!-- Artikel Terakhir Section -->
</x-frontend-layout>
