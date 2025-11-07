<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ __('Data Anggota DPR') }}
            </h2>

        </div>
    </x-slot>)
<div class="p-6 max-w-2xl mx-auto">
    <h1 class="text-xl font-bold mb-4">Edit Anggota DPR</h1>

    <form action="{{ route('anggota-dpr.update', $anggota_dpr->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('anggota_dpr.form', ['anggota' => $anggota_dpr])
        <button class="bg-green-600 text-white px-4 py-2 rounded-md mt-4">Update</button>
    </form>
</div>
</x-app-layout>
