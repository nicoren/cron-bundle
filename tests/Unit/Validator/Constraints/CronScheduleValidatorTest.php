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

        $value = $this->validator->validate('*/20 * * * *', new CronSchedule(["message" => 'Invalid crontab expression.']));
        $this->assertEquals(
            null,
            $value,
            "CronScheduleValidator::validate must return true"
        );
    }

    /**
     * Test bad contab schedule
     *
     * @return void
     */
    public function testBadSchedule()
    {
        $this->expectException(CronException::class);
        $this->validator->validate('0 0 * * * *', new CronSchedule(["message" => 'Invalid crontab expression.']));
    }

    /**
     * Test correct contab schedule with day of weeks comma separated
     *
     * @return void
     */
    public function testCorrectScheduleWithDayOfWeek()
    {
        $value = $this->validator->validate('*/20 * * * Sun,Mon,Tue,Wed,Thu,Fri', new CronSchedule(["message" => 'Invalid crontab expression.']));
        $this->assertEquals(
            null,
            $value,
            "CronScheduleValidator::validate must return true"
        );
    }

    protected function createValidator()
    {
        return new CronScheduleValidator();
    }
}
