<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report; // <-- Pastikan Model Report di-import
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // 1. FITUR MENAMPILKAN SEMUA DATA (Barusan kita buat)
    public function index()
    {
        $query = Report::with(['category', 'user'])->latest();

        if($request=>has('category_id') && $request->category_id != '' ){
            query->where('category_id', $request->category_id);
        }
        if ($request->has('tingkat_keparahan') && $request->tingkat_keparahan != '') {
            $query->where('tingkat_keparahan', $request->tingkat_keparahan);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Mengambil semua laporan beserta data kategori dan user-nya
        $reports = $query->get();        
        return response()->json([
            'message' => 'Berhasil mengambil semua data laporan',
            'data' => $reports
        ], 200);
    }

    // 2. FITUR MENYIMPAN LAPORAN BARU (Yang kemarin)
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'lokasi'      => 'required',
        ]);

        $report = Report::create([
            'user_id'     => auth()->id(), // Otomatis ngambil ID user yang sedang login
            'category_id' => $request->category_id,
            'lokasi'      => $request->lokasi,
            'longitude'   => $request->longitude,
            'latitude'    => $request->latitude,
            'deskripsi'   => $request->deskripsi,
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim!',
            'data'    => $report
        ], 201);
    }

    public function updateStatus(Request $request, $id){
        $request->validate([
            'status' => 'required|in:pending,proses,selesai,ditolak',
        ]);

        $report = Report::find($id);

        if(!$report){
            return response()->json([
                'message' => 'Laporan tidak ditemukan!'
            ], 404);
        }

        $report->status = $request->status;
        $report->save();

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui menjadi ' . $request->status,
            'data' => $report
        ], 200);
    }
}