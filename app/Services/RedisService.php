<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisService
{
    /**
     * Get JSON value by key and auto decode
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = Redis::get($key);
        return $value ? json_decode($value, true) : $default;
    }

    /**
     * Save JSON value with auto encode
     */
    public function set(string $key, mixed $value, int $ttl = null): void
    {
        $json = json_encode($value);

        if ($ttl) {
            Redis::setex($key, $ttl, $json);
        } else {
            Redis::set($key, $json);
        }
    }

    /**
     * Delete key
     */
    public function delete(string $key): void
    {
        Redis::del($key);
    }

    /**
     * Push multiple values to list (no JSON)
     */
    public function listPush(string $key, array $values): void
    {
        if (!empty($values)) {
            Redis::rpush($key, ...$values);
        }
    }

    /**
     * Get list values
     */
    public function listRange(string $key, int $start = 0, int $stop = -1): array
    {
        return Redis::lrange($key, $start, $stop);
    }

    /**
     * Add value to set
     */
    public function setAdd(string $key, mixed $value): void
    {
        Redis::sadd($key, $value);
    }

    /**
     * Check value exists in set
     */
    public function setHas(string $key, mixed $value): bool
    {
        return Redis::sismember($key, $value);
    }

    /**
     * Get set members
     */
    public function setMembers(string $key): array
    {
        return Redis::smembers($key);
    }

    /**
     * Expire key
     */
    public function expire(string $key, int $seconds): void
    {
        Redis::expire($key, $seconds);
    }

    /**
     * Check key exists
     */
    public function exists(string $key): bool
    {
        return Redis::exists($key) > 0;
    }
}
