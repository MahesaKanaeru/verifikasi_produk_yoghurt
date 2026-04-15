<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
    $products = Product::all();
    $nextKode = Product::generateKode();
    return view('produk.index', compact('products', 'nextKode'));
}

public function store(Request $request) {
    $data = $request->validate([
        'nama_produk' => 'required',
        'ukuran' => 'required',
        'estimasi_expired' => 'required|numeric',
        'foto_produk' => 'image|mimes:jpg,png|max:2048',
        'foto_label' => 'image|mimes:jpg,png|max:2048',
    ]);

    $data['kode_produk'] = Product::generateKode();

    if($request->hasFile('foto_produk')) {
        $data['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
    }
    if($request->hasFile('foto_label')) {
        $data['foto_label'] = $request->file('foto_label')->store('label', 'public');
    }

    Product::create($data);
    return back()->with('success', 'Produk berhasil ditambahkan!');
}// Menampilkan form edit (opsional jika pakai modal)

// Update data
public function update(Request $request, Product $produk)
{
    $request->validate([
        'nama_produk' => 'required',
        'ukuran' => 'required',
        'estimasi_expired' => 'required|numeric',
        'foto_produk' => 'nullable|image|mimes:jpg,png|max:2048',
        'foto_label' => 'nullable|image|mimes:jpg,png|max:2048',
    ]);

    $produk->nama_produk = $request->nama_produk;
    $produk->ukuran = $request->ukuran;
    $produk->estimasi_expired = $request->estimasi_expired;

    // Cek jika ada upload Foto Produk baru
    if ($request->hasFile('foto_produk')) {
        $produk->foto_produk = $request->file('foto_produk')->store('produk', 'public');
    }

    // Cek jika ada upload Foto Label baru (TAMBAHAN)
    if ($request->hasFile('foto_label')) {
        $produk->foto_label = $request->file('foto_label')->store('label', 'public');
    }

    $produk->save();

    return back()->with('success', 'Produk berhasil diperbarui!');
}

// Hapus data
public function destroy(Product $produk) {
    $produk->delete();
    return back()->with('success', 'Produk berhasil dihapus!');
}
}
