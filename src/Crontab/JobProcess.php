<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Crontab;

use Nicoren\CronBundle\Model\JobInterface;
use Symfony\Component\Process\Process;

class JobProcess
{

    const STATUS_SUCCESS = "Success";
    const STATUS_ERROR = "Error";
    const STATUS_SKIPPED = "Skipped";

    /**
     * @var Process
     */
    protected ?Process $process = null;

    /**
     * @var JobInterface
     */
    protected JobInterface $job;

    /**
     * @var string
     */
    protected string $status;

    /**
     * @var int
     */
    protected ?int $pid = null;

    /**
     *
     * @param JobInterface $job
     * @param Process $process
     */
    public function __construct(JobInterface $job, ?Process $process = null)
    {
        $this->job = $job;
        $this->process = $process;
        if ($process) {
            $this->pid = $process->getPid();
        }
    }

    /**
     * Return process
     *
     * @return Process
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * Return Job
     *
     * @return JobInterface
     */
    public function getJob(): JobInterface
    {
        return $this->job;
    }

    /**
     * Return status
     *
     * @return string
     */
    public function getStatus(): string
    {
        if (is_null($this->getProcess())) {
            return static::STATUS_SKIPPED;
        }

        if ($this->getProcess()->isSuccessful()) {
            return static::STATUS_SUCCESS;
        }
        return static::STATUS_ERROR;
    }


    /**
     * Return pid
     *
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }
}
