<nav class="bg-gray-800 font-sora" x-data="{ profileOpen: false, mobileMenuOpen: false }">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <!-- Logo and Brand Name -->
            <div class="flex-shrink-0 flex items-center">
                <img src="{{ asset('assets/img/sphinx1.svg') }}" alt="Sphinx.ai Logo" class="h-8 w-auto mr-3">
                <span class="text-xl font-bold text-white">Sphinx.ai</span>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden sm:flex sm:ml-6 space-x-4">
                <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('dashboard') ? 'bg-gray-900 text-white' : '' }}">HR Dashboard</a>
                <a href="{{ route('batches.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('batches.index') ? 'bg-gray-900 text-white' : '' }}">Batches</a>
                <a href="{{ route('batches.processed') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('batches.processed') ? 'bg-gray-900 text-white' : '' }}">Processed Batches</a>
            </div>

            <!-- Profile Dropdown - Desktop -->
            <div class="ml-3 relative" @click.away="profileOpen = false">
                <button @click="profileOpen = !profileOpen" type="button" class="relative flex items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/avatar.svg') }}" alt="Profile Image">
                </button>

                <!-- Dropdown Menu - Desktop -->
                <div x-show="profileOpen" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</button>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="absolute inset-y-0 right-0 flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu, show/hide based on menu state -->
    <div class="sm:hidden" id="mobile-menu" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('dashboard') ? 'bg-gray-900 text-white' : '' }}">HR Dashboard</a>
            <a href="{{ route('batches.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('batches.index') ? 'bg-gray-900 text-white' : '' }}">Batches</a>
            <a href="{{ route('batches.processed') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white {{ Route::is('batches.processed') ? 'bg-gray-900 text-white' : '' }}">Processed Batches</a>
            <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Your Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="block px-3 py-2">
                @csrf
                <button type="submit" class="w-full text-left text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Sign out</button>
            </form>
        </div>
    </div>
</nav>

<script>
    // Alpine.js profile dropdown functionality
    document.addEventListener('alpine:init', () => {
        Alpine.data('navbar', () => ({
            profileOpen: false,
            mobileMenuOpen: false,
        }));
    });
</script>
