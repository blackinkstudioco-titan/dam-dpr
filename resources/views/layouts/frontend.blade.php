<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DAM-DPR') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50">
      <div class="flex justify-between items-center bg-red-600">
        <div class="space-x-8 sm:-my-px sm:ms-10 sm:flex text-gray px-6 py-4 rounded-lg">
            <x-nav-link :href="route('list-artikel')" :active="request()->routeIs('list-artikel')" class="text-white">
                {{ __('Data Artikel') }}
            </x-nav-link>
            <x-nav-link :href="route('foto')" :active="request()->routeIs('foto')" class="text-white">
                {{ __('Data Foto') }}
            </x-nav-link>
        </div>
      </div>
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation_frontend')
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">Tentang Kami</h3>
                        <p class="text-gray-400">
                            Platform dari DPR RI untuk berbagi dan menemukan koleksi foto dan artikel berkualitas tinggi.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">Link Cepat</h3>
                        <ul class="space-y-2">
                            <li><a href="{{route('home')}}" class="text-gray-400 hover:text-white transition">Home</a></li>
                            <li><a href="{{route('foto')}}" class="text-gray-400 hover:text-white transition">Data Foto</a></li>
                            <li><a href="{{route('list-artikel')}}" class="text-gray-400 hover:text-white transition">Data Artikel</a></li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">Kontak</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: info@example.com</li>
                            <li>Telp: (021) 1234-5678</li>
                            <li>Alamat: Jakarta, Indonesia</li>
                        </ul>
            
                    </div>
                </div>

                <div class="border-t border-gray-700 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 DPR RI. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
