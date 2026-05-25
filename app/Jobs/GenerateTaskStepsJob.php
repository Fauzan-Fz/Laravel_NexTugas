<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskStep;
use App\Services\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTaskStepsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;

    /**
     * Create a new job instance.
     * Menerima instance dari model Task.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     * Menggunakan OpenRouterService untuk memecah tugas menjadi langkah-langkah,
     * lalu menyimpan langkah-langkah tersebut ke dalam database.
     */
    public function handle(OpenRouterService $openRouterService): void
    {
        \Illuminate\Support\Facades\Log::info("GenerateTaskStepsJob started for Task ID: {$this->task->id}");

        // Memanggil fungsi generateTaskSteps dari OpenRouterService
        $steps = $openRouterService->generateTaskSteps($this->task->title);

        if ($steps && is_array($steps)) {
            // Menyimpan setiap langkah (sub-tugas) ke database terkait dengan task_id ini
            foreach ($steps as $step) {
                TaskStep::create([
                    'task_id' => $this->task->id,
                    'step_description' => $step,
                    'is_completed' => false,
                ]);
            }
            \Illuminate\Support\Facades\Log::info("GenerateTaskStepsJob completed successfully for Task ID: {$this->task->id}");
        } else {
            \Illuminate\Support\Facades\Log::warning("GenerateTaskStepsJob failed to generate steps for Task ID: {$this->task->id}");
        }
    }
}
