<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */


namespace Nicoren\CronBundle\Crontab;

use Doctrine\Common\Collections\ArrayCollection;

interface RunnerInterface
{
    /**
     * Run jobs as subprocess
     *
     * @return void
     */
    public function run(): void;

    /**
     * Test if subprocess are running
     *
     * @return bool
     */
    public function isRunning(): bool;


    /**
     * Return processes
     *
     * @return ArrayCollection
     */
    public function getProcesses(): ArrayCollection;
}
