<?php
/*
 * Created on Thu Apr 22 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Crontab;

use Nicoren\CronBundle\Doctrine\JobManagerInterface;

class Runner implements RunnerInterface
{
    /**
     * @var JobManagerInterface
     */
    protected JobManagerInterface $jobManager;

    /**
     *
     * @param JobManagerInterface $jobManager
     */
    public function __construct(JobManagerInterface $jobManager)
    {
        $this->jobManager = $jobManager;
    }

    /**
     * Run jobs as subprocess
     *
     * @return void
     */
    public function run(): void
    {

        $jobs = $this->jobManager->find(["enabled" => true]);
        foreach ($jobs as $job) {
        }
    }
}
