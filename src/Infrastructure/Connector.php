<?php

declare(strict_types = 1);

namespace App\Infrastructure;

use App\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private Redis $redis;

    /**
     * @param $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     * @return Cart|null
     * @throws ConnectorException
     */
    public function get(string $key): ?Cart
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $exception) {
            throw new ConnectorException('Connector error', $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $key
     * @param Cart $value
     * @return void
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $exception) {
            throw new ConnectorException('Connector error', $exception->getCode(), $exception);
        }
    }

    public function has($key): bool
    {
        return $this->redis->exists($key);
    }
}
