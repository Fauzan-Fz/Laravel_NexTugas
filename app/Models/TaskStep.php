<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStep extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'step_description',
        'is_completed',
    ];

    /**
     * Casting atribut is_completed ke boolean.
     */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
        ];
    }

    /**
     * Relasi balik ke Task utama.
     * Setiap TaskStep dimiliki oleh satu Task.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
