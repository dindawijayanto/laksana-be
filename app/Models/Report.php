<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'lokasi',
        'longitude',
        'latitude',
        'deskripsi',
        'tingkat_keparahan',
        'foto',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel Users (Satu laporan dimiliki oleh satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}