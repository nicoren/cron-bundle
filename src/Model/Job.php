<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Model;

use DateTime;

class Job implements JobInterface
{
    /**
     * Id
     *
     * @var string|int|null
     */
    protected $id = null;

    /**
     * @var string|null
     */
    protected ?string $name;

    /**
     * @var string|null
     */
    protected ?string $schedule = null;

    /**
     * @var string|null
     */
    protected ?string $command;


    /**
     * @var string|null
     */
    protected ?string $description;

    /**
     * @var bool
     */
    protected bool $enabled = false;


    /**
     * @var bool
     */
    protected ?\DateTime $createdAt = null;


    /**
     * @var bool
     */
    protected ?int $maxConcurrent = null;

    /**
     * Get id
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return Job
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Job
     */
    public function setName(string $name): Job
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get command
     *
     * @return Job
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * Set command
     *
     * @param  string $name
     * @return Job
     */
    public function setCommand(string $command): Job
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Get description
     *
     * @return Job
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param  string $name
     * @return Job
     */
    public function setDescription(string $description): Job
    {
        $this->description = $description;
        return $this;
    }



    /**
     * Get schedule
     *
     * @return Job
     */
    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    /**
     * Set schedule
     *
     * @param  string $schedule
     * @return Job
     */
    public function setSchedule(string $schedule): Job
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return Job
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set schedule
     *
     * @param  string $schedule
     * @return Job
     */
    public function setEnabled(bool $enabled): Job
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set schedule
     *
     * @param  string $schedule
     * @return Job
     */
    public function setCreatedAt(): Job
    {
        $this->createdAt = new \DateTime();
        return $this;
    }

    /**
     * Get max concurrent
     *
     * @return int
     */
    public function getMaxConcurrent(): ?int
    {
        return $this->maxConcurrent;
    }

    /**
     * Set max concurrent
     *
     * @param  string $schedule
     * @return Job
     */
    public function setMaxConcurrent(int $maxConcurrent): Job
    {
        $this->maxConcurrent = $maxConcurrent;
        return $this;
    }
}
