<?php

namespace Nicoren\CronBundle\Storage\Adapter;

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


class Pool implements PoolInterface
{
    /**
     *
     * @var array<AdapterInterface>
     */
    private array $adapters = [];

    public function __construct(?array $adapters = [])
    {
        var_dump($this->adapters);
        $this->adapters = $adapters;
    }

    public function getAdapters(): array
    {
        return $this->adapters;
    }

    public function getAdapter(string $key): AdapterInterface
    {
        return $this->adapters[$key];
    }
}
