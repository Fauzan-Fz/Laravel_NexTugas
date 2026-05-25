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
        Schema::create('task_steps', function (Blueprint $table) {
            // Snowflake ID: unsigned big integer without auto-increment
            $table->unsignedBigInteger('id')->primary();
            // Relasi ke tabel tasks dengan cascade on delete (Snowflake ID)
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // Deskripsi langkah/sub-tugas dari AI
            $table->string('step_description');
            // Status apakah langkah ini sudah selesai
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_steps');
    }
};
