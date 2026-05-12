<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller {
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

        if ($request->hasFile('foto_produk')) {

            $file = $request->file('foto_produk');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->storeAs('produk', $filename, 'public');

            $data['foto_produk'] = 'produk/'.$filename;
        }

        if ($request->hasFile('foto_label')) {

            $file = $request->file('foto_label');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->storeAs('label', $filename, 'public');

            $data['foto_label'] = 'label/'.$filename;
        }

        Product::create($data);
        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, Product $produk) {
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

        if ($request->hasFile('foto_produk')) {

            $file = $request->file('foto_produk');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->storeAs('produk', $filename, 'public');

            $produk->foto_produk = 'produk/'.$filename;
        }

        if ($request->hasFile('foto_label')) {

            $file = $request->file('foto_label');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->storeAs('label', $filename, 'public');

            $produk->foto_label = 'label/'.$filename;
        }
        $produk->save();
        return back()->with('success', 'Produk berhasil diperbarui!');
    }
    public function destroy(Product $produk)
    {
    if ($produk->productions()->exists()) {
        return back()->with('error', 'Produk tidak dapat dihapus karena sudah digunakan di data produksi.');
    }

    $produk->delete();
    return back()->with('success', 'Produk berhasil dihapus!');
    }
}
