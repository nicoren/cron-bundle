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

use Nicoren\CronBundle\Crontab\DateTimeFactory;
use Nicoren\CronBundle\Crontab\Scheduler;
use Nicoren\CronBundle\Crontab\SchedulerInterface;
use Nicoren\CronBundle\Exception\CronException;
use Nicoren\CronBundle\Validator\Constraints\CronSchedule;
use Nicoren\CronBundle\Validator\Constraints\CronScheduleValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CronScheduleValidatorTest extends ConstraintValidatorTestCase
{

    /**
     * Test correct contab schedule
     *
     * @return void
     */
    public function testCorrectSchedule()
    {

        $this->validator->validate('*/20 * * * *', new CronSchedule(message: 'Invalid crontab expression.'));
        $this->assertNoViolation();
    }

    /**
     * Test bad contab schedule
     *
     * @return void
     */
    public function testBadSchedule()
    {
        $this->expectException(CronException::class);
        $this->validator->validate('0 0 * * * *', new CronSchedule(message: 'Invalid crontab expression.'));
    }

    /**
     * Test correct contab schedule with day of weeks comma separated
     *
     * @return void
     */
    public function testCorrectScheduleWithDayOfWeek()
    {
        $this->validator->validate('*/5 * * * Sun,Mon,Tue,Wed,Thu,Fri', new CronSchedule(message: 'Invalid crontab expression.'));
        $this->assertNoViolation();
    }

    protected function createValidator(): CronScheduleValidator
    {
        return new CronScheduleValidator(new Scheduler(new DateTimeFactory('UTC')));
    }
}
