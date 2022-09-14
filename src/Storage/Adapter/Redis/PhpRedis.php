<?php

/**
 * Created on Thu Mar 17 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/

namespace Nicoren\CronBundle\Storage\Adapter\Redis;

use Nicoren\CronBundle\Storage\Adapter\AdapterInterface;

class PhpRedis implements AdapterInterface
{
    private string $key;

    private \Redis $client;

    public function __construct(
        string $key,
        \Redis $client
    ) {
        $this->key = $key;
        $this->client = $client;
    }

    /**
     *
     * @return array
     */
    public function get(): array
    {
        return json_decode($this->client->get($this->key), true) ?? [];
    }

    /**
     *
     * @param array $value
     * @return AdapterInterface
     */
    public function set(array $value): AdapterInterface
    {
        $this->client->set($this->key, json_encode($value));
        return $this;
    }
}
