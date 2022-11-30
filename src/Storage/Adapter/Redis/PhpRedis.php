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
    private string $prefix;

    private \Redis $client;

    public function __construct(
        string $prefix,
        \Redis $client
    ) {
        $this->prefix = $prefix;
        $this->client = $client;
    }

    /**
     *
     * @return array
     */
    public function get(string $pid): array
    {
        return json_decode($this->client->get($this->prefix . $pid), true) ?? [];
    }

    /**
     *
     * @param array $value
     * @return AdapterInterface
     */
    public function set(string $pid, array $value): AdapterInterface
    {
        $this->client->set($this->prefix . $pid, json_encode($value));
        $this->client->save();
        return $this;
    }
}
