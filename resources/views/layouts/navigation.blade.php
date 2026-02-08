<nav x-data="{ open: false }" class="bg-red-600 border-b border-gray-100 text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                  <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">
                      <img src="{{ asset('images/dpr_ri_logo.png') }}" alt="DRP RI LOGO" class="w-12 h-auto">
                  </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-white">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if (auth()->user()?->hasAnyRole(['admin', 'editor', 'uploader']))
                    <x-nav-link :href="route('artikel.index')" :active="request()->routeIs('artikel.*')">
                        {{ __('Data Artikel') }}
                    </x-nav-link>
                    <x-nav-link :href="route('data-foto.index')" :active="request()->routeIs('data-foto.*')">
                        {{ __('Data Foto') }}
                    </x-nav-link>
                    <x-nav-link :href="route('albums.index')" :active="request()->routeIs('albums.index')">
                        {{ __('Album Foto') }}
                    </x-nav-link>
                    @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                        {{ __('Penugasan') }}
                    </x-nav-link>
                    @endif
                    @endif
                    <!-- Data Foto - Tampil untuk admin dan uploader -->
                    @if(auth()->user()->role === 'admin')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Manajemen User') }}
                    </x-nav-link>
                    @endif

                    @if(auth()->user()?->hasAnyRole(['admin', 'editor']))
                    <!-- Dropdown Menu Master Data -->
                     <div class="relative inline-flex items-center" x-data="{ open: false }">
                         <button @click="open = !open"
                                 class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                 {{ request()->routeIs('data-foto.*') ? 'border-indigo-400 text-white focus:border-indigo-700' : 'border-transparent text-gray-300 hover:text-white  focus:text-white focus:border-gray-300' }}">
                             <span>{{ __('Report') }}</span>
                             <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                             </svg>
                         </button>

                         <div x-show="open"
                              @click.away="open = false"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 transform scale-95"
                              x-transition:enter-end="opacity-100 transform scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="opacity-100 transform scale-100"
                              x-transition:leave-end="opacity-0 transform scale-95"
                              class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                              style="display: none;">
                             <div class="py-1">
                                 <a href="{{ route('reports.artikel.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('data-foto.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Artikel') }}
                                 </a>
                                 <a href="{{ route('reports.foto.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('data-foto.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Foto') }}
                                 </a>
                                 <a href="{{ route('reports.akd.foto_akd') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Foto AKD') }}
                                 </a>
                                  <a href="{{ route('reports.akd.artikel_akd') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Artikel AKD') }}
                                 </a>
                                  <a href="{{ route('reports.kegiatan.foto_kegiatan') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Foto Kegiatan') }}
                                 </a>
                                  <a href="{{ route('reports.kegiatan.artikel_kegiatan') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Artikel Kegiatan') }}
                                 </a>
                                <a href="{{ route('reports.anggota_dpr.foto_dpr') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Foto Anggota DPR') }}
                                 </a>
                                 <a href="{{ route('reports.anggota_dpr.artikel_dpr') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Report Artikel Anggota DPR') }}
                                 </a>
                                 <!-- Tambahkan submenu lain di sini jika diperlukan -->
                             </div>
                         </div>
                     </div>
                     @endif

                    @if(auth()->user()?->hasAnyRole(['admin', 'editor']))
                    <!-- Dropdown Menu Master Data -->
                     <div class="relative inline-flex items-center" x-data="{ open: false }">
                         <button @click="open = !open"
                                 class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                 {{ request()->routeIs('data-foto.*') ? 'border-indigo-400 text-white focus:border-indigo-700' : 'border-transparent text-gray-300 hover:text-white  focus:text-white focus:border-gray-300' }}">
                             <span>{{ __('Master Data') }}</span>
                             <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                             </svg>
                         </button>

                         <div x-show="open"
                              @click.away="open = false"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 transform scale-95"
                              x-transition:enter-end="opacity-100 transform scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="opacity-100 transform scale-100"
                              x-transition:leave-end="opacity-0 transform scale-95"
                              class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                              style="display: none;">
                             <div class="py-1">
                                 <a href="{{ route('kategori-foto.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('data-foto.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Kategori') }}
                                 </a>
                                 <a href="{{ route('komisi-dpr.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('data-foto.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('AKD DPR') }}
                                 </a>
                                 <a href="{{ route('anggota-dpr.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->routeIs('anggota-dpr.*') ? 'bg-gray-100 font-semibold' : '' }}">
                                     {{ __('Anggota DPR') }}
                                 </a>
                                 <!-- Tambahkan submenu lain di sini jika diperlukan -->
                             </div>
                         </div>
                     </div>
                     @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
     
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
           

            <div class="mt-3 space-y-1 text-white">
                <x-responsive-nav-link :href="route('home')" class="{{ request()->routeIs('home') ? 'text-gray-900 font-bold' : 'text-white' }}">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')" class="{{ request()->routeIs('profile.*') ? 'text-gray-900 font-bold' : 'text-white' }}">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="{{ request()->routeIs('dashboard') ? 'text-gray-900 font-bold' : 'text-white' }}">
                  {{ __('Dashboard') }}
                </x-responsive-nav-link>
                @if (auth()->user()?->hasAnyRole(['admin', 'editor', 'uploader']))
                <x-responsive-nav-link :href="route('data-foto.index')" :active="request()->routeIs('data-foto.*')" class="{{ request()->routeIs('data-foto.*') ? 'text-gray-900 font-bold' : 'text-white' }} active:text-gray-900">
                    {{ __('Data Foto') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('albums.index')" :active="request()->routeIs('albums.*')" class="{{ request()->routeIs('albums.*') ? 'text-gray-900 font-bold' : 'text-white' }}">
                    {{ __('Album Foto') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('artikel.index')" :active="request()->routeIs('artikel.*')" class="{{ request()->routeIs('artikel.*') ? 'text-gray-900 font-bold' : 'text-white' }}">
                    {{ __('Data Artikel') }}
                </x-responsive-nav-link>
                @if (auth()->user()?->hasAnyRole(['admin', 'editor']))
                 <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')" class="{{ request()->routeIs('events.*') ? 'text-gray-900 font-bold' : 'text-white' }}">
                    {{ __('Penugasan') }}
                </x-responsive-nav-link>
                @endif
                @endif
                @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="{{ request()->routeIs('users.*') ? 'text-gray-900 font-bold' : 'text-white' }}">
                        {{ __('Manajemen User') }}
                </x-responsive-nav-link>
                @endif
             

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
