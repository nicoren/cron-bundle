<?php

namespace Nicoren\CronBundle\Storage\Adapter;

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


interface PoolInterface
{

    public function getAdapters(): array;

    public function getAdapter(string $key): AdapterInterface;
}
