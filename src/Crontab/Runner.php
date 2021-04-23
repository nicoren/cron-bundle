<?php
/*
 * Created on Thu Apr 22 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Crontab;

use Doctrine\Common\Collections\ArrayCollection;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Symfony\Component\Process\Process;

class Runner implements RunnerInterface
{
    /**
     * @var JobManagerInterface
     */
    protected JobManagerInterface $jobManager;

    /**
     * @var ArrayCollection
     */
    protected ArrayCollection $processes;

    /**
     *
     * @var SchedulerInterface
     */
    protected $scheduler;

    /**
     *
     * @param JobManagerInterface $jobManager
     */
    public function __construct(
        JobManagerInterface $jobManager,
        SchedulerInterface $scheduler

    ) {
        $this->jobManager = $jobManager;
        $this->processes = new ArrayCollection();
        $this->scheduler = $scheduler;
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
            if ($this->scheduler->match($job->getSchedule())) {
                $process = new Process([$job->getCommand()]);
                $this->processes->add($process);
                $process->start();
            }
        }
    }

    /**
     * Return true if a process is running
     *
     * @return boolean
     */
    public function isRunning(): bool
    {
        $i = 0;
        while ($i < $this->processes->count() && $this->processes->get($i)->isTerminated()) {
            $i++;
        }
        return $i < $this->processes->count();
    }

    /**
     * Return true if all processes are successfull
     *
     * @return boolean
     */
    public function isSuccessfull(): bool
    {
        $isSuccessfull = true;
        foreach ($this->processes as $process) {
            /**
             * @var Process $process
             */
            $isSuccessfull = $isSuccessfull && $process->isSuccessful();
        }
        return $isSuccessfull;
    }
}
