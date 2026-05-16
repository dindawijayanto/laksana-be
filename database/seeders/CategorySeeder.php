<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() {
        \App\Models\Category::create(['nama_kategori' => 'Jalan Berlubang']);
        \App\Models\Category::create(['nama_kategori' => 'Aspal Mengelupas']);
        \App\Models\Category::create(['nama_kategori' => 'Lampu Jalan Mati']);
        \App\Models\Category::create(['nama_kategori' => 'Trotoar Rusak']);
    }
}
