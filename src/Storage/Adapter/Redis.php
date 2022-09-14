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

    public function get(): array
    {
        return $this->redisClient->get();
    }

    public function set(array $value): self
    {
        $this->redisClient->set($value);
        return $this;
    }
}
