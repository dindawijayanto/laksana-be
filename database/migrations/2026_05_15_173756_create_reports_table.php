<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->string('lokasi');
        $table->double('latitude');
        $table->double('longitude');
        $table->text('deskripsi')->nullable();
        
        $table->integer('tingkat_keparahan')->default(1); 
        
        $table->string('foto')->nullable();
        $table->enum('status', ['pending', 'proses', 'selesai', 'ditolak'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
