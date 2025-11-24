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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['pengguna', 'pengepul', 'admin'])->default('pengguna')->after('email');
            $table->string('address')->nullable()->after('bio');
            $table->decimal('balance', 15, 2)->default(0)->after('address'); // For pengepul earnings
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'address', 'balance']);
        });
    }
};
