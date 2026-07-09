<!-- Sidebar Backdrop -->
<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 transition-transform duration-300 ease-in-out bg-slate-900 border-r border-slate-800 lg:translate-x-0 lg:static lg:inset-auto flex flex-col text-slate-300">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800/80 bg-slate-950/40">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo New Citra" class="h-8 object-contain">
            <span class="font-extrabold text-lg tracking-wider bg-gradient-to-r from-red-500 to-amber-500 bg-clip-text text-transparent">ERP New Citra</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white focus:outline-none transition-colors">
            <i class="ri-close-line text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar Links -->
    <div id="sidebarScrollContainer" class="flex-1 px-4 py-6 space-y-1 overflow-y-auto no-scrollbar">
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="ri-dashboard-line">
            {{ __('Dashboard') }}
        </x-sidebar-link>

        @hasanyrole('Superadmin|Admin')
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Administration</p>
        </div>
        @role('Superadmin')
        <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" icon="ri-group-line">
            {{ __('Users') }}
        </x-sidebar-link>
        @endrole
        <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.*')" icon="ri-settings-4-line">
            {{ __('Pengaturan') }}
        </x-sidebar-link>
        @endhasanyrole

        @hasanyrole('Superadmin|Admin')
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Master Data</p>
        </div>
        <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="ri-truck-line">
            {{ __('Supplier') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('stores.index')" :active="request()->routeIs('stores.*')" icon="ri-store-2-line">
            {{ __('Toko/Mitra') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('sales-teams.index')" :active="request()->routeIs('sales-teams.*')" icon="ri-team-line">
            {{ __('Tim Sales') }}
        </x-sidebar-link>
        @endhasanyrole

        @hasanyrole('Superadmin|Admin|Produser')
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Inventory & Production</p>
        </div>
        <x-sidebar-link :href="route('materials.index')" :active="request()->routeIs('materials.*')" icon="ri-box-3-line">
            {{ __('Bahan Baku') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="ri-product-hunt-line">
            {{ __('Produk & Resep') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')" icon="ri-shopping-cart-line">
            {{ __('Purchasing (PO)') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('goods-receipts.index')" :active="request()->routeIs('goods-receipts.*')" icon="ri-inbox-archive-line">
            {{ __('Penerimaan Barang') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')" icon="ri-stack-line">
            {{ __('Gudang') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('productions.index')" :active="request()->routeIs('productions.*')" icon="ri-tools-line">
            {{ __('Produksi') }}
        </x-sidebar-link>
        @endhasanyrole

        @hasanyrole('Superadmin|Admin|Sales')
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Sales & Consignment</p>
        </div>
        <x-sidebar-link :href="route('consignments.index')" :active="request()->routeIs('consignments.*')" icon="ri-file-list-3-line">
            {{ __('Surat Jalan/ DO') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('returns.index')" :active="request()->routeIs('returns.*')" icon="ri-arrow-go-back-line">
            {{ __('Retur') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('direct-sales.index')" :active="request()->routeIs('direct-sales.*')" icon="ri-money-dollar-circle-line">
            {{ __('Penjualan Langsung') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('cashier-sessions.index')" :active="request()->routeIs('cashier-sessions.*')" icon="ri-cash-line">
            {{ __('Sesi Kasir') }}
        </x-sidebar-link>
        @endhasanyrole

        @hasanyrole('Superadmin|Admin')
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Keuangan</p>
        </div>
        {{-- Sembunyikan Riwayat Invoice seperti request sebelumnya --}}
        {{--
        <x-sidebar-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')" icon="ri-bill-line">
            {{ __('Riwayat Invoice') }}
        </x-sidebar-link>
        --}}
        <x-sidebar-link :href="route('payments.index')" :active="request()->routeIs('payments.*')" icon="ri-bank-card-line">
            {{ __('Pembayaran') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('cash-banks.index')" :active="request()->routeIs('cash-banks.*')" icon="ri-safe-2-line">
            {{ __('Kas & Bank') }}
        </x-sidebar-link>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan</p>
        </div>
        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.index')" icon="ri-line-chart-line">
            {{ __('Laporan Keuangan') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('reports.general-journal')" :active="request()->routeIs('reports.general-journal')" icon="ri-book-open-line">
            {{ __('Jurnal Umum') }}
        </x-sidebar-link>
        @endhasanyrole

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sidebar = document.getElementById("sidebarScrollContainer");
            if (sidebar) {
                // Restore scroll position
                var scrollPosition = localStorage.getItem("sidebar-scroll-position");
                if (scrollPosition) {
                    sidebar.scrollTop = parseInt(scrollPosition, 10);
                }
                
                // Save scroll position on scroll
                sidebar.addEventListener("scroll", function() {
                    localStorage.setItem("sidebar-scroll-position", sidebar.scrollTop);
                });
            }
        });
    </script>
</aside>
