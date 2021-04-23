<?php

/**
 * Created on Tue Apr 13 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Doctrine;


use Nicoren\CronBundle\Model\JobInterface;

interface JobManagerInterface
{

    /**
     * delete job
     * 
     * @param obInterface $job
     * @return void
     */
    public function delete(JobInterface $job): void;

    /**
     * Return class name
     * 
     * @return string
     */
    public function getClass(): string;

    /**
     * Return all jobs
     * 
     * @return JobInterface[]
     */
    public function findOneBy(array $criteria): ?JobInterface;

    /**
     * Return all jobs
     * 
     * @return JobInterface[]
     */
    public function find(): array;

    /**
     * {@inheritdoc}
     */
    public function createEmpty(): JobInterface;

    /**
     * {@inheritdoc}
     */
    public function save(JobInterface $job, $andFlush = true): void;
}
