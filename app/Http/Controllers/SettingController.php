<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data pertama, jika belum ada, buat dengan default
        $settings = Setting::firstOrCreate([], [
            'company_name' => 'New Citra Indonesia',
            'company_address' => 'Jl. Rogojembangan Barat 1 No.31',
            'company_phone' => '081225096633, 082133326959, 085866228323',
            'company_email' => 'info@newcitra.co.id',
            'invoice_font' => "'Helvetica Neue', Helvetica, Arial, sans-serif"
        ]);

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'invoice_font' => 'nullable|string|max:255',
        ]);

        $settings = Setting::first();
        if (!$settings) {
            $settings = new Setting();
        }

        $settings->fill($request->only([
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'invoice_font'
        ]));

        $settings->save();

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
