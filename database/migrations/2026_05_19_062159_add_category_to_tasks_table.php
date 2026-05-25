<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom 'category' ke tabel tasks.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Kolom kategori bersifat opsional (nullable)
            $table->string('category')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
