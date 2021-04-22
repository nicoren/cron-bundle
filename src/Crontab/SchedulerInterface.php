<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Crontab;


interface SchedulerInterface
{



    /**
     * Parse crontab expr
     *
     * @param string $value
     * @return bool
     */
    public function match(string $value): bool;
}
