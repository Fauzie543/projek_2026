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
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'role')) {
                $table->dropColumn('role');
            }

            // Tambahkan kolom 'role_id' sebagai foreign key yang merujuk ke tabel 'roles'
            $table->foreignId('role_id')->nullable()->after('salary_monthly')->constrained('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            // Kembalikan kolom 'role' string jika migrasi di-rollback
            $table->string('role')->nullable()->after('salary_monthly');
        });
    }
};