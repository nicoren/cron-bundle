<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */


namespace Nicoren\CronBundle\Crontab;

use Doctrine\Common\Collections\ArrayCollection;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Nicoren\CronBundle\Model\JobInterface;
use Nicoren\CronBundle\Storage\Adapter\AdapterInterface;
use Nicoren\CronBundle\Storage\Adapter\PoolInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Cache\CacheInterface;

class Runner implements RunnerInterface
{
    const CACHE_KEY = "cron_jobs";

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
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     *
     * @param JobManagerInterface $jobManager
     */
    public function __construct(
        JobManagerInterface $jobManager,
        SchedulerInterface $scheduler,
        AdapterInterface $adapter

    ) {
        $this->jobManager = $jobManager;
        $this->processes = new ArrayCollection();
        $this->scheduler = $scheduler;
        $this->adapter = $adapter;
    }

    /**
     * Return true if job can be runned
     *
     * @param JobInterface $job
     * @return boolean
     */
    protected function canRunProcess(JobInterface $job): bool
    {
        $processes = array_filter($this->adapter->get($job->getId()));

        //job not already start
        if (empty($processes)) {
            return true;
        }

        //job already launched
        if (!empty($processes)) {
            $runningProcesses = [];
            foreach ($processes as $pid) {
                if (posix_kill($pid, 0) === true) {
                    $runningProcesses[] = $pid;
                } else {
                    $this->uncacheProcess($job, $pid);
                }
            }
            if (count($runningProcesses) < $job->getMaxConcurrent()) {
                return true;
            }
        }
        return false;
    }


    /**
     * Return true if job can be runned
     *
     * @param JobInterface $job
     * @return void
     */
    protected function cacheProcess(JobInterface $job, Process $process): void
    {
        $runningProcesses = $this->adapter->get($job->getId());

        $runningProcesses[] = $process->getPid();
        $this->adapter->set($job->getId(), $runningProcesses);
    }

    /**
     * Delete process form cache
     *
     * @param JobInterface $job
     * @return void
     */
    protected function uncacheProcess(JobInterface $job, int $pid): void
    {
        $runningProcesses = $this->adapter->get($job->getId());
        if (!empty($runningProcesses)) {
            $key = array_search($pid, $runningProcesses);
            if ($key !== false) {
                unset($runningProcesses[$key]);
                $this->adapter->set($job->getId(), array_values($runningProcesses));
            }
        }
    }

    /**
     * Run jobs as subprocess
     *
     * @return void
     */
    public function run(?JobInterface $job = null): void
    {
        $force = !is_null($job);
        if ($job) {
            $jobs = [$job];
        } else {
            $jobs = $this->jobManager->find(["enabled" => true]);
        }
        foreach ($jobs as $job) {
            if ($this->scheduler->match($job->getSchedule()) || $force) {
                if ($this->canRunProcess($job)) {
                    $process = Process::fromShellCommandline($job->getCommand());
                    $process->setTimeout(86400);
                    $process->setIdleTimeout(86400);
                    $process->start();
                    $jobProcess = new JobProcess($job, $process);
                    $this->processes->add($jobProcess);
                    $this->cacheProcess($job, $process);
                } else {
                    $jobProcess = new JobProcess($job);
                    $this->processes->add($jobProcess);
                }
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
        $nbProcessTerminated = 0;
        $i = 0;
        while ($i < $this->processes->count()) {

            if (!$this->processes->get($i)->getProcess() || !$this->processes->get($i)->getProcess()->isRunning()) {
                $nbProcessTerminated++;
                /**
                 * @var JobProcess $process
                 */
                if ($process = $this->processes->get($i)) {
                    if ($process->getPid()) {
                        $this->uncacheProcess($process->getJob(), $process->getPid());
                    }
                }
            }
            $i++;
        }
        return $nbProcessTerminated < $this->processes->count();
    }


    public function getProcesses(): ArrayCollection
    {
        return $this->processes;
    }
}
