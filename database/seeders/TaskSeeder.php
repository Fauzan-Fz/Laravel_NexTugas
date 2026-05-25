<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user - Snowflake ID will be auto-generated
        $user = User::create([
            'name' => 'Fauzan Firdaus',
            'email' => 'fauzan@rpl.com',
            'password' => Hash::make('password123'),
        ]);

        $this->command->info("Created user: {$user->name} (ID: {$user->id})");

        // RPL School Tasks with Snowflake IDs
        $tasks = [
            [
                'title' => 'Build API Laravel 13',
                'description' => 'Membangun REST API menggunakan Laravel 13 dengan fitur authentication, middleware, dan eloquent relationships.',
                'category' => 'Web Programming',
                'deadline' => now()->addDays(7),
                'status' => 'Pending',
            ],
            [
                'title' => 'Slicing UI Dashboard',
                'description' => 'Mengkonversi design Figma menjadi HTML/CSS menggunakan Tailwind CSS dengan responsive layout.',
                'category' => 'Web Programming',
                'deadline' => now()->addDays(5),
                'status' => 'Pending',
            ],
            [
                'title' => 'AI SDK Integration',
                'description' => 'Integrasi OpenRouter API untuk generate task steps menggunakan AI (gpt-oss-120b).',
                'category' => 'Web Programming',
                'deadline' => now()->addDays(3),
                'status' => 'Pending',
            ],
            [
                'title' => 'Database Normalization',
                'description' => 'Mendesain skema database untuk sistem manajemen tugas dengan normal form 3NF.',
                'category' => 'Database System',
                'deadline' => now()->addDays(10),
                'status' => 'Pending',
            ],
            [
                'title' => 'SQL Query Optimization',
                'description' => 'Mengoptimalkan query SQL untuk performa tinggi dengan indexing dan query refactoring.',
                'category' => 'Database System',
                'deadline' => now()->addDays(4),
                'status' => 'Pending',
            ],
            [
                'title' => 'UML Diagram System',
                'description' => 'Membuat Use Case, Class Diagram, dan ERD untuk sistem NexTugas.',
                'category' => 'Others',
                'deadline' => now()->addDays(6),
                'status' => 'Pending',
            ],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::create([
                'user_id' => $user->id,
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'category' => $taskData['category'],
                'deadline' => $taskData['deadline'],
                'status' => $taskData['status'],
            ]);

            $this->command->info("Created task: {$task->title} (ID: {$task->id})");
        }

        $this->command->info("\n✅ Seeding completed!");
        $this->command->info("User ID (Snowflake): {$user->id}");
        $this->command->info("Total tasks created: " . count($tasks));
    }
}
