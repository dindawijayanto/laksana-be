<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ============================================================
    //  GET /api/reports  —  semua laporan (admin & user bisa)
    // ============================================================
    public function index(Request $request)
    {
        $reports = Report::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }

    // ============================================================
    //  GET /api/my-reports  —  laporan milik user yang login
    // ============================================================
    public function myReports(Request $request)
    {
        $reports = Report::with('category')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }

    // ============================================================
    //  POST /api/reports  —  buat laporan baru
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'lokasi'            => 'required|string',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'deskripsi'         => 'required|string',
            'tingkat_keparahan' => 'required|integer|min:1|max:5',
            'status'            => 'nullable|string|in:pending,proses,selesai,ditolak',
        ]);

        $report = Report::create([
            'user_id'           => $request->user()->id,
            'category_id'       => $request->category_id,
            'lokasi'            => $request->lokasi,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'deskripsi'         => $request->deskripsi,
            'tingkat_keparahan' => $request->tingkat_keparahan,
            'foto'              => $request->foto ?? null,
            'status'            => $request->status ?? 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil disimpan.',
            'data'    => $report,
        ], 201);
    }

    // ============================================================
    //  PUT /api/reports/{id}/status  —  ADMIN: update status laporan
    // ============================================================
    public function updateStatus(Request $request, $id)
    {
        // Cek apakah user adalah admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya admin yang dapat mengubah status laporan.',
            ], 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,proses,selesai,ditolak',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui.',
            'data'    => $report,
        ]);
    }

    // ============================================================
    //  DELETE /api/reports/{id}  —  hapus laporan
    //  (admin bisa hapus semua, user hanya miliknya sendiri)
    // ============================================================
    public function destroy(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $user = $request->user();

        // Admin boleh hapus semua, user hanya miliknya
        if ($user->role !== 'admin' && $report->user_id !== $user->id) {
            return response()->json([
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $report->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus.',
        ]);
    }

    // ============================================================
    //  GET /api/admin/stats  —  ADMIN: statistik ringkasan dashboard
    // ============================================================
    public function adminStats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $total      = Report::count();
        $pending    = Report::where('status', 'pending')->count();
        $proses     = Report::where('status', 'proses')->count();
        $selesai    = Report::where('status', 'selesai')->count();
        $ditolak    = Report::where('status', 'ditolak')->count();
        $darurat    = Report::whereIn('tingkat_keparahan', [4, 5])->count();

        // Laporan per kategori
        $perKategori = Report::with('category')
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category->name ?? 'Unknown',
                'total'    => $r->total,
            ]);

        return response()->json([
            'total'       => $total,
            'pending'     => $pending,
            'proses'      => $proses,
            'selesai'     => $selesai,
            'ditolak'     => $ditolak,
            'darurat'     => $darurat,
            'per_kategori'=> $perKategori,
        ]);
    }
}