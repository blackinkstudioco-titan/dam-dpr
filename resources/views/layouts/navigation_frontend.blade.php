
<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">
                    <img src="{{ asset('images/dpr_ri_logo.png') }}" alt="DRP RI LOGO" class="w-12 h-auto">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 mx-8">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{request('q')}}"
                        placeholder="Searching data digital..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <input
                        type="hidden"
                        name="cat"
                        value="foto"
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
        </div>
    </div>
</nav>
