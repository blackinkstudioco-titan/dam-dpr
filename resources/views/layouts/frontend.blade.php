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
    <div x-data="{ open: false }" class="bg-red-600">
    <!-- Header -->
    <div class="flex justify-between items-center px-4 py-3">
        <!-- Menu -->
        <div class="text-white font-bold text-lg">
            <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-white px-4 py-2">
               {{ __('Home') }}
            </x-nav-link>
            <x-nav-link :href="route('list-artikel')" :active="request()->routeIs('list-artikel')" class="text-white px-4 py-2">
               {{ __('Data Artikel') }}
            </x-nav-link>
            <x-nav-link :href="route('foto')" :active="request()->routeIs('foto')" class="text-white px-4 py-2">
                {{ __('Data Foto') }}
            </x-nav-link>
        </div>

        <!-- Burger Button (Mobile) -->
        <button @click="open = !open" class="sm:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Desktop Menu -->
        <div class="hidden sm:flex space-x-8">
          &nbsp;
        </div>

        <!-- Login & Register (Desktop) -->
        <div class="hidden sm:flex items-center space-x-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-white hover:text-gray-600 font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-white hover:text-gray-600 font-medium">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-gray-700 font-medium">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" class="sm:hidden px-4 pb-4 space-y-2">
        <!-- Login & Register (Mobile) -->
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-gray-700 hover:text-red-600 font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:text-red-600 font-medium">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Register</a>
                @endif
            @endauth
        @endif
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
                            
                            <li>
                                Jl.Jenderal Gatot Subroto,
                                Senayan Jakarta 10270 - Indonesia
                            </li>
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
