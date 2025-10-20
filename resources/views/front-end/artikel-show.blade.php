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
          <div class="py-10">
              <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                  <div class="bg-white rounded-lg shadow-sm p-6">

                      <div class="flex justify-between items-center mb-6">
                          <h1 class="text-2xl font-bold text-gray-800">{{ $artikel->judul }}</h1>
                  
                      </div>

                      <div class="text-gray-600 mb-4">
                          <p><strong>Tanggal:</strong> {{ $artikel->tanggal ? $artikel->tanggal->format('d M Y') : '-' }}</p>
                          <p><strong>Penulis:</strong> {{ $artikel->penulis ?? '-' }}</p>
                          <p><strong>Sumber:</strong> {{ $artikel->sumber ?? '-' }}</p>
                      </div>

                      @if ($artikel->foto)
                          <div class="mb-6">
                              <img src="{{ asset('storage/' . $artikel->foto) }}" alt="{{ $artikel->judul }}"
                                   class="rounded-lg shadow-md w-full max-h-[450px] object-cover">
                          </div>
                      @endif

                      <div class="prose max-w-none">
                          {!! $artikel->isi !!}
                      </div>

                      @if ($artikel->keyword)
                          <div class="mt-6 pt-4 border-t border-gray-200">
                              <h3 class="text-gray-700 font-semibold mb-2">Kata Kunci:</h3>
                              <div class="flex flex-wrap gap-2">
                                  @foreach (explode(',', $artikel->keyword) as $tag)
                                      <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">#{{ trim($tag) }}</span>
                                  @endforeach
                              </div>
                          </div>
                      @endif

                      <div class="mt-10 text-sm text-gray-500 border-t pt-4">
                          <p><strong>Dibuat oleh:</strong> {{ $artikel->add_by ?? 'N/A' }}</p>
                          <p><strong>Tanggal tambah:</strong> {{ $artikel->add_date ? $artikel->add_date->format('d M Y H:i') : '-' }}</p>
                          @if($artikel->edit_by)
                              <p><strong>Diedit oleh:</strong> {{ $artikel->edit_by }}</p>
                              <p><strong>Tanggal edit:</strong> {{ $artikel->edit_date ? $artikel->edit_date->format('d M Y H:i') : '-' }}</p>
                          @endif
                      </div>
                  </div>
              </div>
          </div>
      </div>




</section>
<!-- Artikel Terakhir Section -->
</x-frontend-layout>
