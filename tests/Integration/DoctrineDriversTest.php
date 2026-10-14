<?php

namespace Nicoren\CronBundle\Tests\Integration;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Nicoren\CronBundle\Tests\Fixtures\App\DriverKernel;
use PHPUnit\Framework\TestCase;

/**
 * Store and read jobs with each supported doctrine driver.
 * The mongodb driver is tested when the MONGODB_URL environment variable is set.
 */
class DoctrineDriversTest extends TestCase
{
    private ?DriverKernel $kernel = null;

    protected function tearDown(): void
    {
        if ($this->kernel) {
            $objectManager = $this->kernel->getContainer()->get('nicoren_cron.object_manager');
            if ($objectManager instanceof DocumentManager) {
                $objectManager->getClient()->dropDatabase('nicoren_cron_bundle_test');
            }
            $this->kernel->shutdown();
        }
    }

    public function testOrm(): void
    {
        $this->kernel = new DriverKernel('orm');
        $this->kernel->boot();
        $entityManager = $this->kernel->getContainer()->get('nicoren_cron.object_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $this->assertJobsAreStored();
    }

    public function testMongoDb(): void
    {
        $url = getenv('MONGODB_URL');
        if (!$url) {
            $this->markTestSkipped('Set MONGODB_URL to test the mongodb driver.');
        }
        $this->kernel = new DriverKernel('mongodb', $url);
        $this->kernel->boot();
        $this->assertInstanceOf(DocumentManager::class, $this->kernel->getContainer()->get('nicoren_cron.object_manager'));

        $this->assertJobsAreStored();
    }

    private function assertJobsAreStored(): void
    {
        $container = $this->kernel->getContainer();
        /** @var JobManagerInterface $manager */
        $manager = $container->get('nicoren_cron.job_manager');

        $job = $manager->createEmpty();
        $job->setName('import')->setCommand('echo import')->setDescription('test')
            ->setSchedule('*/5 * * * *')->setEnabled(true)->setMaxConcurrent(2);
        $manager->save($job);
        $disabled = $manager->createEmpty();
        $disabled->setName('sync')->setCommand('echo sync')->setDescription('test')
            ->setSchedule('0 * * * *')->setEnabled(false)->setMaxConcurrent(1);
        $manager->save($disabled);
        $this->assertNotNull($job->getId());

        $container->get('nicoren_cron.object_manager')->clear();

        $found = $manager->findOneBy(['name' => 'import']);
        $this->assertNotNull($found);
        $this->assertNotSame($job, $found);
        $this->assertSame('echo import', $found->getCommand());
        $this->assertSame('*/5 * * * *', $found->getSchedule());
        $this->assertTrue($found->getEnabled());
        $this->assertSame(2, $found->getMaxConcurrent());
        $this->assertNotNull($found->getCreatedAt());

        $this->assertCount(2, $manager->find());
        $enabled = $manager->find(['enabled' => true]);
        $this->assertCount(1, $enabled);
        $this->assertSame('import', $enabled[0]->getName());

        $manager->delete($found);
        $this->assertCount(1, $manager->find());
    }
}
