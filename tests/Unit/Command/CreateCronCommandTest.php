<?php
/*
 * Created on Thu Apr 22 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Tests\Unit\Command;

use Nicoren\CronBundle\Command\CreateCronCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use PHPUnit\Framework\TestCase;

class CreateCronCommandTest extends TestCase
{

    public function testCreateValidJob()
    {
        $manager = $this->getMockBuilder('Nicoren\CronBundle\Doctrine\JobManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('save');
        $command = new CreateCronCommand($manager);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            "--command" => "echo 'aa'",
            "--description" => "test",
            "--enabled" => true,
            "--max_concurrent" => 1,
            "--name" => "test",
            "--schedule" => "0 0 * * *"
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Job saved.', $output);
    }
}
