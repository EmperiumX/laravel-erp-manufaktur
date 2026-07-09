<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8 text-gray-900">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        
                        <!-- HIDDEN COMPANY INFORMATION -->
                        <input type="hidden" name="company_name" value="{{ $settings->company_name }}">
                        <input type="hidden" name="company_phone" value="{{ $settings->company_phone }}">
                        <input type="hidden" name="company_email" value="{{ $settings->company_email }}">
                        <input type="hidden" name="company_address" value="{{ $settings->company_address }}">

                        <!-- SECTION 2: KUSTOMISASI CETAK DOKUMEN -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                                    <i class="ri-printer-line text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Kustomisasi Dokumen Cetak</h3>
                                    <p class="text-xs text-gray-500">Atur tampilan cetak untuk Invoice, Surat Jalan, dan Nota Penjualan.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Font Utama</label>
                                    <select name="invoice_font" class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm cursor-pointer">
                                        <optgroup label="Sans-serif (Standar & Bersih)">
                                            <option value="'Roboto', 'Inter', 'Segoe UI', Arial, sans-serif" {{ ($settings->invoice_font == "'Roboto', 'Inter', 'Segoe UI', Arial, sans-serif" || empty($settings->invoice_font) || $settings->invoice_font == "'Helvetica Neue', Helvetica, Arial, sans-serif") ? 'selected' : '' }}>Roboto / Inter (Default - Tajam, Tipis & Rekomendasi Printer B&W)</option>
                                            <option value="'Helvetica Neue', Helvetica, Arial, sans-serif" {{ $settings->invoice_font == "'Helvetica Neue', Helvetica, Arial, sans-serif" ? 'selected' : '' }}>Helvetica / Arial (Standar)</option>
                                            <option value="Verdana, Geneva, sans-serif" {{ $settings->invoice_font == "Verdana, Geneva, sans-serif" ? 'selected' : '' }}>Verdana (Lebar & Jelas)</option>
                                            <option value="'Trebuchet MS', Helvetica, sans-serif" {{ $settings->invoice_font == "'Trebuchet MS', Helvetica, sans-serif" ? 'selected' : '' }}>Trebuchet MS (Tegas & Ramping)</option>
                                            <option value="'Segoe UI', Tahoma, Geneva, sans-serif" {{ $settings->invoice_font == "'Segoe UI', Tahoma, Geneva, sans-serif" ? 'selected' : '' }}>Segoe UI (Bersih & Rapi)</option>
                                            <option value="'Century Gothic', sans-serif" {{ $settings->invoice_font == "'Century Gothic', sans-serif" ? 'selected' : '' }}>Century Gothic (Modern & Tipis)</option>
                                            <option value="Impact, Charcoal, sans-serif" {{ $settings->invoice_font == "Impact, Charcoal, sans-serif" ? 'selected' : '' }}>Impact (Sangat Tebal & Padat)</option>
                                        </optgroup>
                                        <optgroup label="Serif (Formal & Klasik)">
                                            <option value="'Times New Roman', Times, serif" {{ $settings->invoice_font == "'Times New Roman', Times, serif" ? 'selected' : '' }}>Times New Roman (Klasik & Formal)</option>
                                            <option value="Georgia, serif" {{ $settings->invoice_font == "Georgia, serif" ? 'selected' : '' }}>Georgia (Elegan & Formal)</option>
                                            <option value="Garamond, serif" {{ $settings->invoice_font == "Garamond, serif" ? 'selected' : '' }}>Garamond (Klasik Halus)</option>
                                            <option value="'Bookman Old Style', serif" {{ $settings->invoice_font == "'Bookman Old Style', serif" ? 'selected' : '' }}>Bookman (Tegas & Formal)</option>
                                            <option value="Palatino, 'Palatino Linotype', Georgia, serif" {{ $settings->invoice_font == "Palatino, 'Palatino Linotype', Georgia, serif" ? 'selected' : '' }}>Palatino (Rapi & Formal)</option>
                                        </optgroup>
                                        <optgroup label="Monospace (Ketikan Mekanis)">
                                            <option value="'Courier New', Courier, monospace" {{ $settings->invoice_font == "'Courier New', Courier, monospace" ? 'selected' : '' }}>Courier New (Mesin Tik / Kasir)</option>
                                        </optgroup>
                                        <optgroup label="Google Web Fonts (Modern & Premium)">
                                            <option value="Inter, sans-serif" {{ $settings->invoice_font == "Inter, sans-serif" ? 'selected' : '' }}>Inter (Tipis, Tajam & Modern)</option>
                                            <option value="Roboto, sans-serif" {{ $settings->invoice_font == "Roboto, sans-serif" ? 'selected' : '' }}>Roboto (Tegas & Sangat Jelas)</option>
                                            <option value="Poppins, sans-serif" {{ $settings->invoice_font == "Poppins, sans-serif" ? 'selected' : '' }}>Poppins (Modern & Simetris)</option>
                                            <option value="Montserrat, sans-serif" {{ $settings->invoice_font == "Montserrat, sans-serif" ? 'selected' : '' }}>Montserrat (Tegas & Elegan)</option>
                                            <option value="'Open Sans', sans-serif" {{ $settings->invoice_font == "'Open Sans', sans-serif" ? 'selected' : '' }}>Open Sans (Bersih & Ringan)</option>
                                            <option value="Lato, sans-serif" {{ $settings->invoice_font == "Lato, sans-serif" ? 'selected' : '' }}>Lato (Tipis & Rapi)</option>
                                            <option value="Nunito, sans-serif" {{ $settings->invoice_font == "Nunito, sans-serif" ? 'selected' : '' }}>Nunito (Bulat & Soft)</option>
                                            <option value="'Playfair Display', serif" {{ $settings->invoice_font == "'Playfair Display', serif" ? 'selected' : '' }}>Playfair Display (Elegan & Mewah)</option>
                                            <option value="Merriweather, serif" {{ $settings->invoice_font == "Merriweather, serif" ? 'selected' : '' }}>Merriweather (Klasik Tebal)</option>
                                            <option value="Oswald, sans-serif" {{ $settings->invoice_font == "Oswald, sans-serif" ? 'selected' : '' }}>Oswald (Tinggi & Tebal)</option>
                                            <option value="'Quicksand', sans-serif" {{ $settings->invoice_font == "'Quicksand', sans-serif" ? 'selected' : '' }}>Quicksand (Tipis & Bulat)</option>
                                        </optgroup>
                                        <optgroup label="Artistic / Cursive (Kreatif & Dekoratif)">
                                            <option value="Pacifico, cursive" {{ $settings->invoice_font == "Pacifico, cursive" ? 'selected' : '' }}>Pacifico (Tulisan Tangan Kuas)</option>
                                            <option value="Lobster, cursive" {{ $settings->invoice_font == "Lobster, cursive" ? 'selected' : '' }}>Lobster (Dekoratif Tebal)</option>
                                            <option value="'Great Vibes', cursive" {{ $settings->invoice_font == "'Great Vibes', cursive" ? 'selected' : '' }}>Great Vibes (Kaligrafi Indah)</option>
                                            <option value="'Comic Sans MS', cursive, sans-serif" {{ $settings->invoice_font == "'Comic Sans MS', cursive, sans-serif" ? 'selected' : '' }}>Comic Sans MS (Santai)</option>
                                        </optgroup>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Font yang Anda pilih di sini akan langsung digunakan saat membuka pratinjau cetak invoice dan surat jalan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl shadow transition-colors flex items-center gap-2">
                                <i class="ri-save-line text-lg"></i>
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
