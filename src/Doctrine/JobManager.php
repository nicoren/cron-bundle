<?php

/**
 * Created on Tue Apr 13 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace FOS\UserBundle\Doctrine;


use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nicoren\CronBundle\Model\JobInterface;

class JobManager implements JobManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->class = $class;
    }

    /**
     * delete job
     * 
     * @param obInterface $job
     * @return void
     */
    public function delete(JobInterface $job): void
    {
        $this->objectManager->remove($job);
        $this->objectManager->flush();
    }

    /**
     * Return class name
     * 
     * @return string
     */
    public function getClass(): string
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * Return all jobs
     * 
     * @return JobInterface[]
     */
    public function findBy(array $criteria): JobInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Return all jobs
     * 
     * @return JobInterface[]
     */
    public function find(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * create and return a new Job
     */
    public function createEmpty(): JobInterface
    {
        $class = $this->getClass();
        $job = new $class();

        return $job;
    }


    /**
     * {@inheritdoc}
     */
    public function save(JobInterface $job, $andFlush = true)
    {
        $this->objectManager->persist($job);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}