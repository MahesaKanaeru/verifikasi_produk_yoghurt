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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('kode_produk')->unique(); // PRD001
        $table->string('nama_produk');
        $table->string('ukuran'); // Misal: 250ml, 500ml
        $table->integer('estimasi_expired'); // Dalam jumlah hari
        $table->string('foto_produk')->nullable();
        $table->string('foto_label')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
