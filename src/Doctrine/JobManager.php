<?php

/**
 * Created on Tue Apr 13 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Doctrine;


use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nicoren\CronBundle\Model\JobInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class, ValidatorInterface $validator)
    {
        $this->objectManager = $om;
        $this->class = $class;
        $this->validator = $validator;
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
    public function findOneBy(array $criteria): ?JobInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Return jobs
     * 
     * @return JobInterface[]
     */
    public function find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        if (!empty($criteria)) {
            return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
        }
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
    public function save(JobInterface $job, $andFlush = true): void
    {
        $this->validator->validate($job);
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
