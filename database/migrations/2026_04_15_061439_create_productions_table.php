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
            $table->string('production_code')->unique(); // Contoh: VY00001
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->date('production_date');
            $table->date('expiration_date');
            $table->string('qr_code_path')->nullable();
            $table->string('final_label_path')->nullable(); // Sesuai dengan blade kamu sebelumnya
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};