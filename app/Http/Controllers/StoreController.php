<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Mitra,Agen,Distributor,Reseller,End User,Maklon',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Store::create($request->all());
        return redirect()->route('stores.index')->with('success', 'Data Toko berhasil ditambahkan!');
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Mitra,Agen,Distributor,Reseller,End User,Maklon',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $store->update($request->all());
        return redirect()->route('stores.index')->with('success', 'Data Toko berhasil diperbarui!');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Data Toko berhasil dihapus!');
    }
}