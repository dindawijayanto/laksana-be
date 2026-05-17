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
        // 1. Buat User Biasa (Menyimpan hasil ke variabel $user)
        $user = User::updateOrCreate(
            ['email' => 'keizaandrea@gmail.com'], // Diperbaiki agar sinkron
            [
                'name' => 'Micheline Keiza',
                'email' => 'keizaandrea@gmail.com', // 🌟 Tadi kurang koma di sini
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        // 2. Buat User Admin (ID: 3 seperti di HeidiSQL kamu)
        $admin = User::updateOrCreate(
            ['email' => 'admin@laksana.id'],
            [
                'name'     => 'Admin Laksana',
                'email'    => 'admin@laksana.id',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // 3. Masukkan Kategori Infrastruktur
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

        // 4. Masukkan Dummy Reports
        $listStatus = ['pending', 'proses', 'selesai', 'ditolak'];
        
        $dummyReports = [
            ['lokasi' => 'Gerbang Veteran UB', 'lat' => -7.952, 'lng' => 112.614, 'cat' => 1],
            ['lokasi' => 'Jl. Sumbersari No. 10', 'lat' => -7.955, 'lng' => 112.612, 'cat' => 2],
            ['lokasi' => 'Depan FILKOM UB', 'lat' => -7.953, 'lng' => 112.615, 'cat' => 3],
            ['lokasi' => 'Makam Pahlawan Jl. Bandung', 'lat' => -7.960, 'lng' => 112.618, 'cat' => 4],
            ['lokasi' => 'Kawasan Danau Toba Sawojajar', 'lat' => -7.982, 'lng' => 112.656, 'cat' => 1],
        ];

        foreach ($dummyReports as $data) {
            Report::create([
                'user_id' => $user->id, // 🌟 Sekarang aman karena $user di atas sudah didefinisikan (ID: 1)
                'category_id' => $data['cat'],
                'lokasi' => $data['lokasi'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'deskripsi' => 'Laporan uji coba otomatis dari system seeder.',
                'tingkat_keparahan' => rand(1, 5), 
                'status' => $listStatus[array_rand($listStatus)], 
            ]);
        }
    }
}