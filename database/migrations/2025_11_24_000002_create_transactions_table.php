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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained()->onDelete('cascade');
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade'); // User yang jual
            $table->foreignId('pengepul_id')->nullable()->constrained('users')->onDelete('set null'); // Pengepul yang beli
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 15, 2);
            $table->decimal('admin_fee', 15, 2)->default(0); // Komisi admin
            $table->decimal('pengepul_earnings', 15, 2)->default(0); // Pendapatan pengepul
            $table->text('notes')->nullable(); // Catatan dari pengepul/admin
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
