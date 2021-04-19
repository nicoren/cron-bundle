<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nicoren\CronBundle\Tests\Unit\Validator\Constraints;

use Nicoren\CronBundle\Exception\CronException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Nicoren\CronBundle\Validator\Constraints\CronScheduleValidator;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Twig\Loader\LoaderInterface;

abstract class CronScheduleValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var MockObject|ControllerResolverInterface
     */
    protected $controllerResolver;

    /**
     * @var MockObject|EngineInterface|LoaderInterface
     */
    protected $engine;

    protected function setUp(): void
    {
        $this->controllerResolver = $this->createMock(ControllerResolverInterface::class);
        $this->engine = $this->mockEngine();

        parent::setUp();
    }

    /**
     * @return MockObject|EngineInterface|LoaderInterface
     */
    abstract protected function mockEngine();

    public function testCorrectSchedule()
    {

        $value = $this->validator->validate('*/20 * * * *', new CronScheduleValidator());
        $this->assertEquals(
            true,
            $value,
            "CronScheduleValidator::validate must return true"
        );
    }

    public function testBadSchedule()
    {
        $this->expectException(CronException::class);
        $this->validator->validate('0 0 * * aa', new CronScheduleValidator());
    }

    public function testCorrectScheduleWithDayOfWeek()
    {
        $value = $this->validator->validate('*/20 * * * Sun,Mon,Tue,Wed,Thu,Fri', new CronScheduleValidator());
        $this->assertEquals(
            true,
            $value,
            "CronScheduleValidator::validate must return true"
        );
    }
}
