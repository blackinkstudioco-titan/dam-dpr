@php
    use Illuminate\Support\Str;
@endphp
<x-frontend-layout>

  <!-- Hero Banner -->
  <!-- Opsi 2: Background Image dari Storage Laravel -->
  <section class="relative bg-cover bg-no-repeat text-white py-20" style="background-image: url('{{ asset('images/background.jpg') }}'); background-position: 30% 20%;">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4">Selamat Datang di Digital Asset Manajemen</h1>
            <p class="text-xl mb-8">Database Foto & Artikel Dewan Perwakilan Rakyat RI</p>
            <a href="#" class="inline-block px-8 py-3 bg-white text-red-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                Jelajahi Sekarang
            </a>
        </div>
    </div>
</section>
<!-- Foto Terakhir Section -->
<section class="py-16 bg-white">
      <div class="container mx-auto px-4">
          <div class="flex items-center justify-between mb-8">
              <h2 class="text-3xl font-bold text-gray-800">Foto Terakhir</h2>
              <a href="{{ route('foto', '') }}" class="text-red-600 hover:text-red-700 font-medium">
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
                                  <p class="text-gray-200 text-sm">{{ $foto->created_at->format('d M Y') }}</p>
                              </div>
                          </div>
                    </a>

                  @endforeach
              </div>
          @endif
        </div>
</section>
<!-- Foto Terakhir Section -->
<section class="py-16 bg-white">
      <div class="container mx-auto px-4">
          <div class="flex items-center justify-between mb-8">
              <h2 class="text-3xl font-bold text-gray-800">Artikel Terakhir</h2>
              <a href="{{ route('list-artikel', '') }}" class="text-red-600 hover:text-red-700 font-medium">
                  Lihat Semua →
              </a>
          </div>

          {{-- Grid List --}}
              @if ($artikels->isEmpty())
                  <div class="text-center text-gray-500 py-10">
                      Tidak ada artikel ditemukan.
                  </div>
              @else
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                      @foreach ($artikels as $artikel)
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
              @endif
      </div>
</section>
<!-- Artikel Terakhir Section -->
</x-frontend-layout>
