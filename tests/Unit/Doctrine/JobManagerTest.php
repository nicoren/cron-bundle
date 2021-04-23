<?php
/*
 * Created on Thu Apr 22 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Tests\Unit\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nicoren\CronBundle\Doctrine\JobManager;
use Nicoren\CronBundle\Model\Job;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobManagerTest extends KernelTestCase
{

    private $document;

    private $objectManager;

    private $objectRepository;

    /**
     *
     * @var JobManager
     */
    private $jobManager;

    /**
     * Undocumented variable
     *
     * @var ValidatorInterface
     */
    private $validator;


    public function setUp(): void
    {
        static::bootKernel();
        $om = parent::$kernel->getContainer()->get('doctrine_mongodb');
        $this->validator = \Symfony\Component\Validator\Validation::createValidator();
        $this->document = new Job();
        $this->document->setCommand("echo 'aa'")
            ->setDescription("test")
            ->setEnabled(true)
            ->setMaxConcurrent(1)
            ->setName("Test")
            ->setSchedule("0 0 * * *");
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->jobManager = new JobManager($this->objectManager, Job::class, $this->validator);
        $this->objectRepository = $this->createMock(ObjectRepository::class);

        $this->objectManager
            ->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(Job::class))
            ->will($this->returnValue($this->objectRepository));
    }



    /**
     * delete job
     * 
     * @param obInterface $job
     * @return void
     */
    public function testDelete(): void
    {
        $this->objectManager
            ->expects($this->once())
            ->method('remove');

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->jobManager->delete($this->document);
    }

    /**
     * delete job
     * 
     * @param obInterface $job
     * @return void
     */
    public function testFindOneBy(): void
    {
        $repository = $this->objectRepository;

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(["id" => 123]))
            ->will($this->returnValue($this->document));
        $this->jobManager->findOneBy(["id" => 123]);
    }

    /**
     * delete job
     * 
     * @param obInterface $job
     * @return void
     */
    public function testFind(): void
    {
        $repository = $this->objectRepository;
        $repository
            ->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue([$this->document]));
        $this->jobManager->find();
        $this->assertTrue(true);
    }

    /**
     * {@inheritdoc}
     */
    public function testSave()
    {
        $this->validator->validate($this->document);
        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->document);

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->jobManager->save($this->document);
    }
}
