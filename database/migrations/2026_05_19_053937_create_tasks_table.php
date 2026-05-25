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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // Kolom judul tugas sekolah
            $table->string('title');
            // Kolom deskripsi (bisa null)
            $table->text('description')->nullable();
            // Kolom deadline waktu
            $table->dateTime('deadline')->nullable();
            // Kolom status dengan default 'Pending'
            $table->enum('status', ['Pending', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
