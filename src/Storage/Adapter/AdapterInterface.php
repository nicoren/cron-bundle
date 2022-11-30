<?php

namespace Nicoren\CronBundle\Storage\Adapter;

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


interface AdapterInterface
{
    /**
     *
     * @return array
     */
    public function get(string $pid): array;

    /**
     *
     * @param array $value
     * @return AdapterInterface
     */
    public function set(string $pid, array $value): AdapterInterface;
}
