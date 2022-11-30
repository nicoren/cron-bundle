<?php

namespace Nicoren\CronBundle\Storage\Adapter;




/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


class Redis implements AdapterInterface
{
    private AdapterInterface $redisClient;

    public function __construct(AdapterInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     *
     * @return array
     */
    public function get(string $pid): array
    {
        return $this->redisClient->get($pid);
    }

    /**
     *
     * @param array $value
     * @return AdapterInterface
     */
    public function set(string $pid, array $value): AdapterInterface
    {
        $this->redisClient->set($pid, $value);
        return $this;
    }
}
