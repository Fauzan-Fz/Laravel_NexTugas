<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    /**
     * Memanggil API OpenRouter untuk memecah tugas utama menjadi 3 sub-tugas/langkah.
     *
     * @param string $taskTitle
     * @return array|null
     */
    public function generateTaskSteps(string $taskTitle): ?array
    {
        // URL API OpenRouter
        $url = 'https://openrouter.ai/api/v1/chat/completions';
        
        // [Security] Membaca API Key melalui konfigurasi config/services.php
        // Pemisahan credential ini sangat penting demi aspek keamanan dan mencegah hardcoding credential
        // secara langsung pada logika bisnis/layanan yang berjalan.
        $apiKey = config('services.openrouter.api_key');

        // Prompt sistem untuk meminta AI merespon dalam format JSON array yang bersih
        $prompt = "You are a task breakdown AI assistant. The main task is: '{$taskTitle}'. "
                . "Break this task down into 3 concrete sub-tasks or steps. "
                . "Return ONLY a JSON array of strings, for example: [\"Step 1\", \"Step 2\", \"Step 3\"]. "
                . "Do not add explanations, markdown, or any other text, just the pure JSON array.";

        try {
            // Melakukan HTTP POST request ke OpenRouter
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url'), // Digunakan OpenRouter untuk analitik
                'X-Title' => config('app.name'),
            ])->post($url, [
                'model' => 'openai/gpt-oss-120b:free', // Model sesuai permintaan
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['choices'][0]['message']['content'])) {
                    $content = $data['choices'][0]['message']['content'];

                    // ROBUST SANITIZATION: Strip markdown formatting and clean LLM response
                    $content = $this->sanitizeJsonResponse($content);

                    // Mendecode JSON string menjadi array PHP
                    $steps = json_decode($content, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('JSON decoding failed in OpenRouter API. Raw content: ' . $content);
                    }

                    if (is_array($steps)) {
                        return $steps;
                    }
                }
            } else {
                Log::error('OpenRouter API Response Error: ' . $response->body());
            }

            return null;

        } catch (\Exception $e) {
            // Log jika terjadi kesalahan koneksi
            Log::error('OpenRouter API Exception (Connection failed): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sanitize LLM response by stripping markdown formatting and extracting JSON.
     * Handles edge cases like triple backticks, whitespace, and embedded text.
     *
     * @param string $content Raw LLM response content
     * @return string Cleaned JSON string ready for parsing
     */
    private function sanitizeJsonResponse(string $content): string
    {
        // Trim whitespace
        $content = trim($content);

        // Remove markdown code block markers (```json, ```javascript, ```, etc.)
        $content = preg_replace('/^```[\w]*\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        // Remove any remaining backticks
        $content = str_replace('`', '', $content);

        // Extract JSON array if surrounded by other text/explanations
        if (preg_match('/\[.*\]/s', $content, $matches)) {
            $content = $matches[0];
        }

        // Final trim
        return trim($content);
    }
}
