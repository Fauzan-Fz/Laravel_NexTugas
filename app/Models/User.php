<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasSnowflake;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasSnowflake;

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
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * Hidden attributes for serialization.
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Eloquent relationship: A User has many Tasks.
     * Used for scoping queries to the authenticated user's tasks only.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
