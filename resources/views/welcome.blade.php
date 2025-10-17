<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Digital - Home</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-red-600">
                        LOGO
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 mx-8">
                    <form action="#" method="GET" class="relative">
                        <input
                            type="text"
                            name="q"
                            placeholder="Searching data digital..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Login & Register -->
                <div class="flex items-center space-x-4">
                    <a href="#" class="px-4 py-2 text-gray-700 hover:text-red-600 font-medium">
                        Login
                    </a>
                    <a href="#" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-red-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-5xl font-bold mb-4">Selamat Datang di Portal Digital</h1>
                <p class="text-xl mb-8">Temukan koleksi foto dan artikel digital terbaik untuk kebutuhan Anda</p>
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
                <a href="#" class="text-red-600 hover:text-red-700 font-medium">
                    Lihat Semua →
                </a>
            </div>

            <!-- Row 1 - 4 Foto -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Foto 1 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop"
                        alt="Pemandangan Gunung"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Pemandangan Gunung</h3>
                            <p class="text-gray-200 text-sm">2 jam yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 2 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=400&h=300&fit=crop"
                        alt="Hutan Tropis"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Hutan Tropis</h3>
                            <p class="text-gray-200 text-sm">5 jam yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 3 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?w=400&h=300&fit=crop"
                        alt="Padang Rumput"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Padang Rumput</h3>
                            <p class="text-gray-200 text-sm">1 hari yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 4 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop"
                        alt="Sunset di Pantai"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Sunset di Pantai</h3>
                            <p class="text-gray-200 text-sm">1 hari yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2 - 4 Foto -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Foto 5 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=400&h=300&fit=crop"
                        alt="Danau di Pegunungan"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Danau di Pegunungan</h3>
                            <p class="text-gray-200 text-sm">2 hari yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 6 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1426604966848-d7adac402bff?w=400&h=300&fit=crop"
                        alt="Jalur Pendakian"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Jalur Pendakian</h3>
                            <p class="text-gray-200 text-sm">3 hari yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 7 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=400&h=300&fit=crop"
                        alt="Pantai Pasir Putih"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Pantai Pasir Putih</h3>
                            <p class="text-gray-200 text-sm">3 hari yang lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Foto 8 -->
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=400&h=300&fit=crop"
                        alt="Langit Berbintang"
                        class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg">Langit Berbintang</h3>
                            <p class="text-gray-200 text-sm">4 hari yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artikel Terakhir Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Artikel Terakhir</h2>
                <a href="#" class="text-red-600 hover:text-red-700 font-medium">
                    Lihat Semua →
                </a>
            </div>

            <!-- Row 1 - 4 Artikel -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Artikel 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=300&fit=crop"
                        alt="Teknologi AI"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Perkembangan Teknologi AI di Era Digital
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Artificial Intelligence semakin berkembang pesat dan mengubah cara kita bekerja dan berinteraksi dalam kehidupan sehari-hari.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">10 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=400&h=300&fit=crop"
                        alt="Tips Fotografi"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            7 Tips Fotografi Landscape untuk Pemula
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Pelajari teknik dasar fotografi landscape untuk menghasilkan foto pemandangan yang memukau dan profesional.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">09 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400&h=300&fit=crop"
                        alt="Destinasi Wisata"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            10 Destinasi Wisata Tersembunyi di Indonesia
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Jelajahi keindahan Indonesia dengan mengunjungi destinasi wisata tersembunyi yang belum banyak diketahui wisatawan.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">08 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 4 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=300&fit=crop"
                        alt="Kuliner Nusantara"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Kelezatan Kuliner Nusantara yang Menggugah Selera
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Nikmati kekayaan cita rasa kuliner tradisional Indonesia yang beragam dari Sabang sampai Merauke.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">07 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2 - 4 Artikel -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Artikel 5 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1493723843671-1d655e66ac1c?w=400&h=300&fit=crop"
                        alt="Budaya Indonesia"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Kekayaan Budaya dan Seni Tradisional Indonesia
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Mengenal lebih dekat warisan budaya dan seni tradisional yang menjadi identitas bangsa Indonesia.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">06 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 6 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop"
                        alt="Tips Traveling"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Panduan Traveling Hemat untuk Backpacker
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Tips dan trik traveling dengan budget minim namun tetap bisa menikmati perjalanan yang berkesan.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">05 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 7 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400&h=300&fit=crop"
                        alt="Web Development"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Belajar Web Development dari Nol hingga Mahir
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Roadmap lengkap untuk menjadi web developer profesional dengan menguasai teknologi terkini.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">04 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Artikel 8 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img
                        src="https://images.unsplash.com/photo-1490730141103-6cac27aaab94?w=400&h=300&fit=crop"
                        alt="Gaya Hidup Sehat"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            Menerapkan Gaya Hidup Sehat di Tengah Kesibukan
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            Cara mudah menjalani pola hidup sehat meski memiliki rutinitas yang padat dan jadwal yang sibuk.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">03 Okt 2025</span>
                            <a href="#" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Tentang Kami</h3>
                    <p class="text-gray-400">
                        Platform digital untuk berbagi dan menemukan koleksi foto dan artikel berkualitas tinggi.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Link Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Foto</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Artikel</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Tentang</a></li>
                    </ul>
                </div>

                <!-- Kategori -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Teknologi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Wisata</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Kuliner</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Lifestyle</a></li>
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
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Portal Digital. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
