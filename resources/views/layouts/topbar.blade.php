<header class="sticky top-0 z-30 flex items-center justify-between px-6 py-3.5 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-200">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="text-slate-500 lg:hidden hover:text-slate-800 transition-colors focus:outline-none">
            <i class="ri-menu-line text-2xl"></i>
        </button>
        <!-- Mobile Header Brand Logo & Name -->
        <div class="flex items-center gap-2 lg:hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Logo New Citra" class="h-8 object-contain">
            <span class="font-extrabold text-sm tracking-wider bg-gradient-to-r from-red-600 to-amber-500 bg-clip-text text-transparent">New Citra</span>
        </div>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Settings Dropdown -->
        <div class="relative">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2.5 p-1.5 text-sm font-medium text-gray-600 transition-all duration-200 bg-gray-50 border border-gray-100 rounded-xl hover:bg-gray-100 hover:text-gray-900 focus:outline-none">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-600 to-red-800 text-white flex items-center justify-center font-bold shadow-sm shadow-red-500/15">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider mt-1">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                        </div>
                        <i class="ri-arrow-down-s-line text-gray-400 text-lg"></i>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        <i class="ri-user-settings-line mr-2"></i> {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:text-red-700">
                            <i class="ri-logout-box-r-line mr-2"></i> {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
