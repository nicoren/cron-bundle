<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Crontab\Scheduler;

interface ValidatorInterface
{
    /**
     * Validate cron schedule format
     *
     * @return boolean
     */
    public function validate(string $jobSchedule): bool;
}
