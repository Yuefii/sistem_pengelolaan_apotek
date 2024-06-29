<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;

class TransaksiPDFController extends Controller
{
    public function downloadPDF(Request $request)
    {
        $query = Transaksi::with(['obat', 'user']);

        if ($request->has('tableFilters')) {
            $filters = $request->input('tableFilters');

            if (isset($filters['obat_id']['value']) && $filters['obat_id']['value'] != null) {
                $query->where('obat_id', $filters['obat_id']['value']);
            }

            if (isset($filters['user_id']['value']) && $filters['user_id']['value'] != null) {
                $query->where('user_id', $filters['user_id']['value']);
            }

            if (isset($filters['created_from']['value']) && $filters['created_from']['value'] != null) {
                $query->whereDate('tanggal', '>=', $filters['created_from']['value']);
            }

            if (isset($filters['created_until']['value']) && $filters['created_until']['value'] != null) {
                $query->whereDate('tanggal', '<=', $filters['created_until']['value']);
            }
        }

        $transaksis = $query->get();

        $pdf = FacadePdf::loadView('pdf.transaksi', compact('transaksis'));
        return $pdf->download('laporan_transaksi.pdf');
    }
}
