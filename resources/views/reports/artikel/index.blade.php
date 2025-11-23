<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Artikel') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.artikel.export.excel', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('reports.artikel.export.pdf', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filter Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filter Report</h3>
                    
                    <form method="GET" action="{{ route('reports.artikel.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            {{-- Report Type --}}
                            <div>
                                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Report
                                </label>
                                <select name="report_type" id="report_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="upload" {{ $reportType === 'upload' ? 'selected' : '' }}>
                                        Berdasarkan Upload (Draft)
                                    </option>
                                    <option value="edit" {{ $reportType === 'edit' ? 'selected' : '' }}>
                                        Berdasarkan Edit (Published)
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $reportType === 'upload' ? 'Data dari tabel artikel (draft)' : 'Data dari tabel artikel_publish' }}
                                </p>
                            </div>
                            {{-- user --}}
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $reportType === 'upload' ? 'Penulis' : 'Editor' }}
                                </label>
                                <select name="user_id" id="user_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua {{ $reportType === 'upload' ? 'Penulis' : 'Editor' }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role_name }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                            {{-- Start Date --}}
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="start_date" 
                                       value="{{ $startDate }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            {{-- End Date --}}
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Akhir
                                </label>
                                <input type="date" name="end_date" id="end_date" 
                                       value="{{ $endDate }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            {{--
                           
                            <div>
                                <label for="rubrik" class="block text-sm font-medium text-gray-700 mb-2">
                                    Komisi
                                </label>
                                <select name="rubrik" id="rubrik" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Rubrik</option>
                                    @foreach($rubriks as $rubrikItem)
                                        <option value="{{ $rubrikItem }}" {{ $rubrik === $rubrikItem ? 'selected' : '' }}>
                                            {{ $rubrikItem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            --}}
                            {{-- Status Filter --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select name="status" id="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Saja</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Saja</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Per Page --}}
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                    Data Per Halaman
                                </label>
                                <select name="per_page" id="per_page" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Tampilkan Report
                            </button>
                            <a href="{{ route('reports.artikel.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Alert Info --}}
            @if($reportType === 'edit')
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Info:</strong> Report ini menampilkan data dari tabel <code class="bg-blue-100 px-1 rounded">artikel_publish</code> (artikel yang sudah di-publish/edit).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Total Artikel</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_artikel']) }}</div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $reportType === 'upload' ? 'Draft' : 'Published' }}
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Active</div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_active']) }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Inactive</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['total_inactive']) }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Deleted</div>
                        <div class="text-2xl font-bold text-red-600">{{ number_format($stats['total_deleted']) }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Dengan Foto</div>
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['artikel_with_foto']) }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Total Rubrik</div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_rubrik']) }}</div>
                    </div>
                </div>
            </div>

            {{-- Report Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">
                            Report {{ $reportType === 'upload' ? 'Upload (Draft)' : 'Edit (Published)' }} Artikel
                            @if($startDate || $endDate)
                                <span class="text-sm text-gray-500">
                                    ({{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal' }} - 
                                     {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }})
                                </span>
                            @endif
                        </h3>
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $artikels->firstItem() ?? 0 }} - {{ $artikels->lastItem() ?? 0 }} dari {{ $artikels->total() }} data
                        </div>
                    </div>

                    @if($artikels->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No
                                        </th>
                                   
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Penugasan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul / Rubrik
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Penulis
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Penugasan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $reportType === 'upload' ? 'Tanggal Upload' : 'Tanggal Edit (Publish)' }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            @if($reportType === 'edit')
                                                Di Edit Oleh
                                            @else
                                                Penulis
                                            @endif
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Foto
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($artikels as $index => $artikel)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $artikels->firstItem() + $index }}
                                            </td>
                                      
                                            <td class="px-6 py-4  text-sm">
                                                {{ $artikel->event?->nama_event ? $artikel->event->nama_event : '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ Str::limit($artikel->judul, 60) }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $artikel->rubrik ?? 'Tanpa Rubrik' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    @if($reportType === 'upload')
                                                     {{ $artikel->creator->name ?? '-' }}
                                                    @else
                                                      {{ $artikel->artikelDraft->creator->name ?? '-' }}
                                                    @endif
                                                </div>
                                             
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                {{ $artikel->event?->tanggal ? $artikel->event->tanggal->format('d M Y H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4  text-sm text-gray-500">
                                                @if($reportType === 'upload')
                                                    {{ $artikel->add_date ? $artikel->add_date->format('d M Y H:i') : '-' }}
                                                @else
                                                    {{ $artikel->edit_date ? $artikel->edit_date->format('d M Y H:i') : '-' }}
                                                @endif
                                            </td>
 <td class="px-6 py-4 whitespace-nowrap">
    @if($reportType === 'edit')
        {{-- Tampilkan editor dari relasi --}}
        @if($artikel->editor)
            <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                        <span class="text-xs font-medium text-white">
                            {{ strtoupper(substr($artikel->editor->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $artikel->editor->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $artikel->editor->role_badge_color }}-100 text-{{ $artikel->editor->role_badge_color }}-800">
                            {{ $artikel->editor->role_name }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <span class="text-gray-400">Unknown User</span>
        @endif
    @else
        {{-- Tampilkan creator dari relasi --}}
        @if($artikel->creator)
            <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-green-600 flex items-center justify-center">
                        <span class="text-xs font-medium text-white">
                            {{ strtoupper(substr($artikel->creator->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $artikel->creator->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $artikel->creator->role_badge_color }}-100 text-{{ $artikel->creator->role_badge_color }}-800">
                            {{ $artikel->creator->role_name }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <span class="text-gray-400">Unknown User</span>
        @endif
    @endif
</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($artikel->del == 1)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Deleted
                                                    </span>
                                                @elseif($artikel->active == 1)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($artikel->foto)
                                                    <span class="text-green-600">✓ Ada</span>
                                                @else
                                                    <span class="text-gray-400">✗ Tidak</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $artikels->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Tidak ada data artikel yang ditemukan dengan filter yang dipilih.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>