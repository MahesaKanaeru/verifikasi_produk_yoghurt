<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            // $table->string('production_num-ber')->unique();   // hapus
            $table->string('production_code', 500);         // cipher dari production_number
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('qty')->default(0);     // jumlah pcs / botol
            $table->date('production_date');
            $table->string('expiration_date', 500);         // cipher dari format Ymd
            $table->string('qr_code_path')->nullable();
            $table->string('final_label_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};