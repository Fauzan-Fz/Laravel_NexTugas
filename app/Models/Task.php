<?php

namespace App\Models;

use App\Traits\HasSnowflake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, HasSnowflake;

    /**
     * Disable auto-increment for Snowflake IDs.
     */
    public $incrementing = false;

    /**
     * Snowflake IDs are integers.
     */
    protected $keyType = 'int';

    /**
     * Mass-assignable attributes.
     * 'user_id' is included so store() can associate the task with the authenticated user.
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'deadline',
        'status',
    ];

    /**
     * Cast deadline to a Carbon datetime instance.
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    /**
     * Eloquent relationship: A Task belongs to one User (the owner).
     * Used for scoping and data privacy enforcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Eloquent relationship: A Task has many TaskSteps (AI-generated sub-tasks).
     */
    public function steps(): HasMany
    {
        return $this->hasMany(TaskStep::class);
    }
}
