<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
          </svg>
          {{ __('Detail Artikel') }}
      </h2>
  </x-slot>

  <div class="py-10">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white rounded-lg shadow-sm p-6">

              <div class="flex justify-between items-center mb-6">
                  <h1 class="text-2xl font-bold text-gray-800">{{ $artikel->judul }}</h1>
                  <a href="{{ route('artikel.index') }}"
                     class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg transition">
                      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                      </svg>
                      Kembali
                  </a>
              </div>

              <div class="text-gray-600 mb-4">
                  <p><strong>Tanggal:</strong> {{ $artikel->tanggal ? $artikel->tanggal->format('d M Y') : '-' }}</p>
                  <p><strong>Penulis:</strong> {{ $artikel->penulis ?? '-' }}</p>
                  <p><strong>Penugasan:</strong> {{ $artikel->event->nama_event ?? '-' }}</p>
                  <p><strong>Alat Kelengkapan DPR:</strong> {{ $artikel->komisiDPR->nama_komisi ?? '-' }}</p>
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
                  <p><strong>Dibuat oleh:</strong> {{ $artikel->creator->name ?? 'N/A' }}</p>
                  <p><strong>Tanggal tambah:</strong> {{ $artikel->add_date ? $artikel->add_date->format('d M Y H:i') : '-' }}</p>
                  @if($artikel->edit_by)
                      <p><strong>Diedit oleh:</strong> {{ $artikel->edit_by }}</p>
                      <p><strong>Tanggal edit:</strong> {{ $artikel->edit_date ? $artikel->edit_date->format('d M Y H:i') : '-' }}</p>
                  @endif
              </div>
          </div>
      </div>
  </div>

  <style>
      .prose img {
          border-radius: 8px;
          margin-top: 1rem;
          margin-bottom: 1rem;
      }
      .prose p {
          margin-bottom: 1rem;
          line-height: 1.75;
      }
  </style>
</x-app-layout>
