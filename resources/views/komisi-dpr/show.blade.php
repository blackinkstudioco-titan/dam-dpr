<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kelengkapan DPR (AKD): ') }} {{ $komisiDpr->nama_komisi }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('komisi-dpr.edit', $komisiDpr->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('komisi-dpr.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Informasi AKD</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama AKD</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $komisiDpr->nama_komisi }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bidang</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @if($komisiDpr->bidang)
                                    <p class="text-gray-900 whitespace-pre-line">{{ $komisiDpr->bidang }}</p>
                                @else
                                    <p class="text-gray-500 italic">Bidang belum diisi</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat</label>
                                <p class="text-gray-900">{{ $komisiDpr->created_at->format('d M Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                                <p class="text-gray-900">{{ $komisiDpr->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Button -->
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('komisi-dpr.destroy', $komisiDpr->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus komisi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Hapus Komisi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>