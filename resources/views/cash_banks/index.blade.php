<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Kas & Bank') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Total Kas</p>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Total Bank</p>
                    <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($totalBank, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Total Keseluruhan</p>
                    <p class="text-2xl font-bold text-purple-700">Rp {{ number_format($totalCash + $totalBank, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
                    @endif

                    <a href="{{ route('cash-banks.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4 font-bold">
                        <i class="ri-add-line mr-1"></i> Tambah Akun Kas/Bank
                    </a>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($cashBanks as $cb)
                        <div class="border rounded-lg p-4 hover:shadow-md transition {{ $cb->is_active ? '' : 'opacity-50' }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-lg">{{ $cb->name }}</h3>
                                    <span class="text-xs px-2 py-0.5 rounded {{ $cb->type === 'Cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $cb->type }}</span>
                                    @if(!$cb->is_active) <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700 ml-1">Nonaktif</span> @endif
                                </div>
                                <a href="{{ route('cash-banks.edit', $cb->id) }}" class="text-gray-400 hover:text-gray-600"><i class="ri-settings-3-line"></i></a>
                            </div>
                            @if($cb->type === 'Bank')
                                <p class="text-sm text-gray-500">{{ $cb->bank_name }} - {{ $cb->account_number }}</p>
                            @endif
                            <p class="text-2xl font-bold mt-2 {{ $cb->balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rp {{ number_format($cb->balance, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $cb->transactions_count }} transaksi</p>
                            <a href="{{ route('cash-banks.show', $cb->id) }}" class="mt-3 inline-block text-blue-600 hover:underline text-sm font-bold">Lihat Transaksi →</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
