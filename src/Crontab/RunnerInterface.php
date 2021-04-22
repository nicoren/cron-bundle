<?php
/*
 * Created on Thu Apr 22 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Crontab;

interface RunnerInterface
{
    /**
     * Run jobs as subprocess
     *
     * @return void
     */
    public function run(): void;
}
