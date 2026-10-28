<?php

/**
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Tests\Unit\Crontab;

use Nicoren\CronBundle\Crontab\JobProcess;
use Nicoren\CronBundle\Crontab\Runner;
use Nicoren\CronBundle\Crontab\SchedulerInterface;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Nicoren\CronBundle\Model\Job;
use Nicoren\CronBundle\Storage\Adapter\AdapterInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class RunnerTest extends TestCase
{
    private function createRunner(int $timeout): Runner
    {
        $pids = [];
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('get')->willReturnCallback(function (string $id) use (&$pids) {
            return $pids[$id] ?? [];
        });
        $adapter->method('set')->willReturnCallback(function (string $id, array $value) use (&$pids, $adapter) {
            $pids[$id] = $value;
            return $adapter;
        });

        return new Runner(
            $this->createMock(JobManagerInterface::class),
            $this->createMock(SchedulerInterface::class),
            $adapter,
            $timeout
        );
    }

    private function createJob(string $command): Job
    {
        $job = new Job();
        (fn() => $this->id = 'job')->call($job);
        $job->setCommand($command);
        $job->setSchedule('* * * * *');
        $job->setMaxConcurrent(1);
        return $job;
    }

    private function wait(Runner $runner): JobProcess
    {
        while ($runner->isRunning()) {
            usleep(10000);
        }
        return $runner->getProcesses()->first();
    }

    public function testJobExceedingTimeoutIsKilled(): void
    {
        $runner = $this->createRunner(1);
        $start = microtime(true);
        $runner->run($this->createJob('sleep 30'));
        $jobProcess = $this->wait($runner);

        $this->assertLessThan(5, microtime(true) - $start);
        $this->assertSame(JobProcess::STATUS_TIMEOUT, $jobProcess->getStatus());
    }

    public function testTimedOutJobSubprocessesAreKilled(): void
    {
        $pidFile = tempnam(sys_get_temp_dir(), 'cron');
        $runner = $this->createRunner(1);
        //sh does not exec the last command of a list : the sleep is a subprocess of the shell
        $runner->run($this->createJob("true && sh -c 'echo \$\$ > $pidFile; exec sleep 30'"));
        $jobProcess = $this->wait($runner);
        $pid = (int) file_get_contents($pidFile);
        unlink($pidFile);

        $this->assertSame(JobProcess::STATUS_TIMEOUT, $jobProcess->getStatus());
        $this->assertGreaterThan(0, $pid);
        $this->assertFalse(file_exists("/proc/$pid") && !str_contains(file_get_contents("/proc/$pid/stat"), ') Z '), "subprocess $pid still running");
    }

    public function testJobWithinTimeoutSucceeds(): void
    {
        $runner = $this->createRunner(10);
        $runner->run($this->createJob('true'));

        $this->assertSame(JobProcess::STATUS_SUCCESS, $this->wait($runner)->getStatus());
    }
}
