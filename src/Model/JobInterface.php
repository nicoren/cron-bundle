<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Model;

interface JobInterface
{

    /**
     * Get id
     *
     * @return int|string|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return Job
     */
    public function getName(): ?string;

    /**
     * Set name
     *
     * @param  string $name
     * @return Job
     */
    public function setName(string $name): Job;

    /**
     * Get command
     *
     * @return Job
     */
    public function getCommand(): ?string;

    /**
     * Set command
     *
     * @param  string $name
     * @return Job
     */
    public function setCommand(string $command): Job;

    /**
     * Get description
     *
     * @return Job
     */
    public function getDescription(): ?string;

    /**
     * Set description
     *
     * @param  string $name
     * @return Job
     */
    public function setDescription(string $description): Job;


    /**
     * Get schedule
     *
     * @return Job
     */
    public function getSchedule(): ?string;
    /**
     * Set schedule
     *
     * @param  string $schedule
     * @return Job
     */
    public function setSchedule(string $schedule): Job;

    /**
     * Get enabled
     *
     * @return Job
     */
    public function getEnabled(): bool;

    /**
     * Set schedule
     *
     * @param  string $schedule
     * @return Job
     */
    public function setEnabled(bool $enabled): Job;
}
