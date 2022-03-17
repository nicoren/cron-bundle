<?php

namespace Nicoren\CronBundle\Storage\Adapter;

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


interface AdapterInterface
{
    public function get(): array;

    public function set(array $value): self;
}
