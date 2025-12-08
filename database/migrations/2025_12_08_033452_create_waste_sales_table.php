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
        Schema::create('waste_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('waste_type'); // Jenis sampah: plastik, kertas, logam, kaca, organik
            $table->decimal('weight', 8, 2); // Berat dalam kg
            $table->decimal('price_per_kg', 10, 2); // Harga per kg
            $table->decimal('total_price', 10, 2); // Total harga
            $table->text('description')->nullable(); // Deskripsi tambahan
            $table->string('photo_path')->nullable(); // Path foto sampah
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_sales');
    }
};
