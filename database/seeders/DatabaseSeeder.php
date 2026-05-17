<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT USER ADMIN DEFAULT (email:
        $user = User::updateOrCreate(
            ['email' => 'admin@laksana.com'],
            [
                'name' => 'Admin Laksana',
                'password' => Hash::make('password123'),
                'role' => 'admin',  // ← tambah ini
            ]
        );

        // 2. BUAT 7 JENIS INFRASTRUKTUR
        $categories = [
            ['id' => 1, 'nama_kategori' => 'Jalan & Jembatan'],
            ['id' => 2, 'nama_kategori' => 'Drainase'],
            ['id' => 3, 'nama_kategori' => 'Penerangan (PJU)'],
            ['id' => 4, 'nama_kategori' => 'Trotoar'],
            ['id' => 5, 'nama_kategori' => 'Fasilitas Umum'],
            ['id' => 6, 'nama_kategori' => 'Bangunan Publik'],
            ['id' => 7, 'nama_kategori' => 'Listrik & Utilitas'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['id' => $category['id']], $category);
        }

        // 3. BUAT DATA LAPORAN DUMMY (Biar Grafik & Peta Langsung Terisi)
        // Kita siapkan pilihan status dan tingkat keparahan acak
        $listStatus = ['pending', 'proses', 'selesai', 'ditolak'];
        
        // Contoh 5 data laporan di sekitar Malang/Kampus
        $dummyReports = [
            ['lokasi' => 'Gerbang Veteran UB', 'lat' => -7.952, 'lng' => 112.614, 'cat' => 1],
            ['lokasi' => 'Jl. Sumbersari No. 10', 'lat' => -7.955, 'lng' => 112.612, 'cat' => 2],
            ['lokasi' => 'Depan FILKOM UB', 'lat' => -7.953, 'lng' => 112.615, 'cat' => 3],
            ['lokasi' => 'Makam Pahlawan Jl. Bandung', 'lat' => -7.960, 'lng' => 112.618, 'cat' => 4],
            ['lokasi' => 'Kawasan Danau Toba Sawojajar', 'lat' => -7.982, 'lng' => 112.656, 'cat' => 1],
        ];

        foreach ($dummyReports as $data) {
            Report::create([
                'user_id' => $user->id,
                'category_id' => $data['cat'],
                'lokasi' => $data['lokasi'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'deskripsi' => 'Laporan uji coba otomatis dari system seeder.',
                'tingkat_keparahan' => rand(1, 5), // Mengisi acak angka 1 sampai 5
                'status' => $listStatus[array_rand($listStatus)], // Mengisi acak status pending/proses/selesai/ditolak
            ]);
        }
    }
}