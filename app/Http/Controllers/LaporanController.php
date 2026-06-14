<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Services\AesService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;  

class LaporanController extends Controller
{
    // ── Query helper ─────────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = Production::with('product')->whereHas('product')->latest();

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('production_date', [
                $request->dari,
                $request->sampai,
            ]);
        } elseif ($request->filled('dari')) {
            $query->whereDate('production_date', '>=', $request->dari);
        } elseif ($request->filled('sampai')) {
            $query->whereDate('production_date', '<=', $request->sampai);
        }

        return $query;
    }

    // ── Decrypt expiry helper ─────────────────────────────────────────
    private function mapProductions($query, AesService $aes)
    {
        return $query->get()->map(function ($prod) use ($aes) {
            try {
                // Decrypt expiration date
                $plain = $aes->decrypt($prod->expiration_date);
                $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');

                // ← Tambah ini: decrypt production_code juga
                $prod->production_code = $aes->decrypt($prod->production_code);

            } catch (\Exception $e) {
                $prod->plain_expiry    = '-';
                $prod->production_code = '-'; // fallback kalau decrypt gagal
            }
            return $prod;
        });
    }

    // ── Halaman Laporan (web) ─────────────────────────────────────────
  public function index(Request $request, AesService $aes)
    {
        $query = Production::with('product')->whereHas('product')->latest();

        $dari   = $request->filled('dari') ? $request->dari : now('Asia/Jakarta')->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : $dari;

        $query->whereBetween('production_date', [$dari, $sampai]);

        $productions = $query->get()->map(function ($prod) use ($aes) {
            try {
                // Decrypt expiration date
                $plain = $aes->decrypt($prod->expiration_date);
                $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');

                // ← Tambah ini
                $prod->production_code = $aes->decrypt($prod->production_code);

            } catch (\Exception $e) {
                $prod->plain_expiry    = '-';
                $prod->production_code = '-';
            }
            return $prod;
        });

        $grouped  = $productions->groupBy(fn($p) => Carbon::parse($p->production_date)->format('Y-m-d'));
        $totalQty = $productions->sum('qty');

        return view('laporan.index', compact('grouped', 'totalQty', 'productions', 'dari', 'sampai'));
    }
    // ── Export PDF ────────────────────────────────────────────────────
    public function pdf(Request $request, AesService $aes)
    {
        $productions = $this->mapProductions($this->buildQuery($request), $aes);

        $grouped  = $productions->groupBy(fn($p) => Carbon::parse($p->production_date)->format('Y-m-d'));
        $totalQty = $productions->sum('qty');

        // Susun nama file berdasarkan filter periode
        $dari   = $request->dari   ? Carbon::parse($request->dari)->format('Ymd')   : 'awal';
        $sampai = $request->sampai ? Carbon::parse($request->sampai)->format('Ymd') : 'akhir';
        $filename = "laporan_produksi_{$dari}_{$sampai}.pdf";

        $pdf = Pdf::loadView('laporan.pdf', compact('productions', 'grouped', 'totalQty'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
        // Gunakan ->download($filename) jika ingin langsung diunduh
    }
}