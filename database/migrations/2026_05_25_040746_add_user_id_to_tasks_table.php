<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add user_id to tasks table so that each task is owned by a specific user.
     * Data privacy: all queries will be scoped to auth()->id() to prevent cross-user data leaks.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Foreign key referencing the users table — nullable to keep existing rows valid
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn('user_id');
        });
    }
};
