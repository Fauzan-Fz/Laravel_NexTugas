<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Snowflake ID Generator Trait
 * 
 * Generates unique 64-bit integers based on the Snowflake algorithm.
 * Structure: 41 bits timestamp + 10 bits worker ID + 12 bits sequence
 */
trait HasSnowflake
{
    /**
     * Boot the trait - hook into the creating event
     */
    public static function bootHasSnowflake(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->id)) {
                $model->id = static::generateSnowflakeId();
            }
        });
    }

    /**
     * Generate a unique 64-bit Snowflake ID
     */
    public static function generateSnowflakeId(): int
    {
        // Custom epoch (January 1, 2024)
        $epoch = 1704067200000;
        
        // Current timestamp in milliseconds
        $timestamp = (int) (microtime(true) * 1000);
        
        // Timestamp relative to epoch (41 bits)
        $time = $timestamp - $epoch;
        
        // Worker ID (10 bits - 0 to 1023)
        $workerId = getmypid() % 1024;
        
        // Sequence number (12 bits - 0 to 4095)
        // Use static counter with time-based reset
        static $lastTimestamp = 0;
        static $sequence = 0;
        
        if ($time === $lastTimestamp) {
            $sequence = ($sequence + 1) & 0xFFF; // 12 bits mask
            if ($sequence === 0) {
                // Wait for next millisecond if sequence overflows
                while ($time <= $lastTimestamp) {
                    $timestamp = (int) (microtime(true) * 1000);
                    $time = $timestamp - $epoch;
                }
            }
        } else {
            $sequence = 0;
        }
        
        $lastTimestamp = $time;
        
        // Compose 64-bit ID: (timestamp << 22) | (workerId << 12) | sequence
        $id = (($time & 0x1FFFFFFFFFF) << 22) | (($workerId & 0x3FF) << 12) | ($sequence & 0xFFF);
        
        return $id;
    }

    /**
     * Extract timestamp from a Snowflake ID
     */
    public static function getTimestampFromId(int $id): int
    {
        $epoch = 1704067200000;
        $time = ($id >> 22) & 0x1FFFFFFFFFF;
        return $time + $epoch;
    }
}
