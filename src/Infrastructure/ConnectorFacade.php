<?php

declare(strict_types = 1);

namespace App\Infrastructure;

use Redis;
use RedisException;

class ConnectorFacade
{
    public string $host;
    public int $port = 6379;
    public ?string $password = null;
    public ?int $dbIndex = null;
    public Connector $connector;

    /**
     * @param $host
     * @param $port
     * @param $password
     * @param $dbIndex
     * @throws ConnectorException
     */
    public function __construct($host, $port, $password, $dbIndex)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbIndex = $dbIndex;
        $this->build();
    }

    /**
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * @return void
     * @throws ConnectorException
     */
    protected function build(): void
    {
        $redis = new Redis();
        try {
            if (!$redis->connect($this->host, $this->port)) {
                throw new RedisException("Connection failed");
            }

            if ($this->password && !$redis->auth($this->password)) {
                throw new RedisException("Authentication failed");
            }

            if ($this->dbIndex !== null && !$redis->select($this->dbIndex)) {
                throw new RedisException("DB selection failed");
            }

            $this->connector = new Connector($redis);

        } catch (RedisException $exception) {
            throw new ConnectorException(
                "Redis connection error: " . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
