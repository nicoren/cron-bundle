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
    private $id = null;



    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var string|null
     */
    private ?string $schedule = null;

    /**
     * @var string|null
     */
    private ?string $command;


    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var boolean
     */
    private bool $enabled = false;


    /**
     * @var boolean
     */
    private ?\DateTime $createdAt = null;

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
     * @return Job
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
    public function setCreatedAt(\DateTime $createdAt): Job
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
