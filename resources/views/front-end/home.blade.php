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
                                  <p class="text-gray-200 text-sm">{{ $foto->tgl_mm->format('d M Y') }}</p>
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
