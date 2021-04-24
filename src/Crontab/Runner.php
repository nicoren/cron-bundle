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
     * @var CacheInterface
     */
    protected $cache;

    /**
     *
     * @param JobManagerInterface $jobManager
     */
    public function __construct(
        JobManagerInterface $jobManager,
        SchedulerInterface $scheduler,
        CacheInterface $cache

    ) {
        $this->jobManager = $jobManager;
        $this->processes = new ArrayCollection();
        $this->scheduler = $scheduler;
        $this->cache = $cache;
    }

    /**
     * Return true if job can be runned
     *
     * @param JobInterface $job
     * @return boolean
     */
    protected function canRunProcess(JobInterface $job): bool
    {
        $processes = $this->getCacheItem()->get();
        //job not already start
        if (!isset($processes[$job->getId()])) {
            return true;
        }

        //job already launched
        if (isset($processes[$job->getId()])) {
            $runningProcesses = [];
            foreach ($processes[$job->getId()] as $pid) {

                if (posix_kill($pid, 0)) {
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
     * Return cached running process
     *
     * @return CacheItem
     */
    protected function getCacheItem(): CacheItem
    {
        /**
         * @var CacheItem
         */
        $cacheItem = $this->cache->getItem(static::CACHE_KEY);

        return $cacheItem;
    }

    /**
     * Return true if job can be runned
     *
     * @param JobInterface $job
     * @return void
     */
    protected function cacheProcess(JobInterface $job, Process $process): void
    {
        $cacheItem = $this->getCacheItem();
        $runningProcesses = $cacheItem->get();
        if (!$runningProcesses) {
            $runningProcesses = [];
        }

        if (!isset($runningProcesses[$job->getId()])) {
            $runningProcesses[$job->getId()] = [];
        }
        $runningProcesses[$job->getId()][] = $process->getPid();
        $cacheItem->set($runningProcesses);
        $this->cache->save($cacheItem);
    }

    /**
     * Delete process form cache
     *
     * @param JobInterface $job
     * @return void
     */
    protected function uncacheProcess(JobInterface $job, int $pid): void
    {
        $cacheItem = $this->getCacheItem();
        $runningProcesses = $cacheItem->get();
        if (isset($runningProcesses[$job->getId()])) {
            if (($key = array_search($pid, $runningProcesses[$job->getId()])) !== false) {
                unset($runningProcesses[$job->getId()][$key]);
            }
            $cacheItem->set($runningProcesses);
            $this->cache->save($cacheItem);
        }
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
                if ($this->canRunProcess($job)) {
                    $process = Process::fromShellCommandline($job->getCommand());
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
                if ($process = $this->processes->get($i)->getProcess()) {
                    $this->uncacheProcess($this->processes->get($i)->getJob(), $this->processes->get($i)->getPid());
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
