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
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Runner implements RunnerInterface
{
    public const CACHE_KEY = "cron_jobs";

    /**
     * Seconds left to a timed out job to stop after SIGTERM before SIGKILL
     */
    public const KILL_GRACE_PERIOD = 10;

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
     * Maximum duration of a job in seconds (0 = no limit)
     *
     * @var int
     */
    protected int $timeout;

    /**
     * Path of the setsid binary, false when it is not available
     *
     * @var string|false|null
     */
    protected $setsid = null;

    /**
     *
     * @param JobManagerInterface $jobManager
     */
    public function __construct(
        JobManagerInterface $jobManager,
        SchedulerInterface $scheduler,
        AdapterInterface $adapter,
        int $timeout = 86400
    ) {
        $this->jobManager = $jobManager;
        $this->processes = new ArrayCollection();
        $this->scheduler = $scheduler;
        $this->adapter = $adapter;
        $this->timeout = $timeout;
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
                    $process = Process::fromShellCommandline($this->getShellCommand($job->getCommand()));
                    //the timeout is enforced by isRunning() which kills the whole process group
                    $process->setTimeout(null);
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
            /**
             * @var JobProcess $jobProcess
             */
            $jobProcess = $this->processes->get($i);
            $process = $jobProcess->getProcess();
            if ($process && $this->timeout && $process->isRunning() && microtime(true) - $process->getStartTime() > $this->timeout) {
                $this->kill($process);
                $jobProcess->setTimedOut(true);
            }

            if (!$jobProcess->getProcess() || !$jobProcess->getProcess()->isRunning()) {
                $nbProcessTerminated++;
                if ($jobProcess->getPid()) {
                    $this->uncacheProcess($jobProcess->getJob(), $jobProcess->getPid());
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


    /**
     * Run the command in its own process group (session) when setsid is available,
     * so that a timed out job is killed with all its subprocesses : sh does not exec the
     * last command of a list ("mkdir -p var/log && php bin/console ..."), killing only
     * the shell would leave the command running
     *
     * @param string $command
     * @return string
     */
    protected function getShellCommand(string $command): string
    {
        if (is_null($this->setsid)) {
            $this->setsid = '\\' !== \DIRECTORY_SEPARATOR ? ((new ExecutableFinder())->find('setsid') ?? false) : false;
        }
        if ($this->setsid === false) {
            return $command;
        }
        return 'exec ' . escapeshellarg($this->setsid) . ' sh -c ' . escapeshellarg($command);
    }

    /**
     * Kill a job and its subprocesses : SIGTERM, then SIGKILL after the grace period
     *
     * @param Process $process
     * @return void
     */
    protected function kill(Process $process): void
    {
        if (!$this->setsid) {
            $process->stop(static::KILL_GRACE_PERIOD);
            return;
        }
        $processGroup = -$process->getPid();
        posix_kill($processGroup, 15);
        $deadline = microtime(true) + static::KILL_GRACE_PERIOD;
        //isRunning() reaps the shell, the group exists while one of its processes is alive
        while (($process->isRunning() || posix_kill($processGroup, 0)) && microtime(true) < $deadline) {
            usleep(100000);
        }
        if ($process->isRunning() || posix_kill($processGroup, 0)) {
            posix_kill($processGroup, 9);
        }
        $process->stop(0);
    }
}
