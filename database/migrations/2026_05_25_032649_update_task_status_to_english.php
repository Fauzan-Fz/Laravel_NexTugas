<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending', 'Completed', 'Belum Selesai', 'Selesai') DEFAULT 'Pending'");
        DB::table('tasks')->where('status', 'Belum Selesai')->update(['status' => 'Pending']);
        DB::table('tasks')->where('status', 'Selesai')->update(['status' => 'Completed']);
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending', 'Completed') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending', 'Completed', 'Belum Selesai', 'Selesai') DEFAULT 'Belum Selesai'");
        DB::table('tasks')->where('status', 'Pending')->update(['status' => 'Belum Selesai']);
        DB::table('tasks')->where('status', 'Completed')->update(['status' => 'Selesai']);
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Belum Selesai', 'Selesai') DEFAULT 'Belum Selesai'");
    }
};
