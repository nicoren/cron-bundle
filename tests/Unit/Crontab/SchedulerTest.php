<?php

namespace Nicoren\CronBundle\Tests\Unit\Crontab;

use Nicoren\CronBundle\Crontab\DateTimeFactoryInterface;
use Nicoren\CronBundle\Crontab\Scheduler;
use Nicoren\CronBundle\Exception\CronException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SchedulerTest extends TestCase
{
    private function schedulerAt(string $now): Scheduler
    {
        return new Scheduler(new class ($now) implements DateTimeFactoryInterface {
            public function __construct(private string $now) {}

            public function createDateTime(?string $date = null): \DateTime
            {
                return new \DateTime($this->now, new \DateTimeZone('UTC'));
            }
        });
    }

    public static function schedules(): iterable
    {
        // schedule, time, expected
        yield 'every 2 hours at 14:00' => ['0 */2 * * *', '2026-10-19 14:00', true];
        yield 'every 2 hours at 15:00' => ['0 */2 * * *', '2026-10-19 15:00', false];
        yield 'every 2 hours at 14:01' => ['0 */2 * * *', '2026-10-19 14:01', false];
        yield 'at minute 30 at 14:30' => ['30 * * * *', '2026-10-19 14:30', true];
        yield 'at minute 30 at 14:00' => ['30 * * * *', '2026-10-19 14:00', false];
        yield 'every day at 3:00' => ['0 3 * * *', '2026-10-19 03:00', true];
        yield 'every day at 3:00, at 14:00' => ['0 3 * * *', '2026-10-19 14:00', false];
        yield 'every 20 minutes at 14:40' => ['*/20 * * * *', '2026-10-19 14:40', true];
        yield 'every 20 minutes at 14:45' => ['*/20 * * * *', '2026-10-19 14:45', false];
        yield 'every 7 minutes at 01:00' => ['*/7 * * * *', '2026-10-19 01:00', true];
        yield 'every 7 minutes at 01:03' => ['*/7 * * * *', '2026-10-19 01:03', false];
        yield 'step from range start at 14:15' => ['5-59/10 * * * *', '2026-10-19 14:15', true];
        yield 'step from range start at 14:10' => ['5-59/10 * * * *', '2026-10-19 14:10', false];
        yield 'every 2 days from the 1st, the 19th' => ['0 0 */2 * *', '2026-10-19 00:00', true];
        yield 'every 2 days from the 1st, the 20th' => ['0 0 */2 * *', '2026-10-20 00:00', false];
        yield 'list of hours' => ['0 8,12,18 * * *', '2026-10-19 12:00', true];
        yield 'week days (monday)' => ['*/5 * * * mon-fri', '2026-10-19 10:05', true];
        yield 'week days (sunday)' => ['*/5 * * * mon-fri', '2026-10-18 10:05', false];
        yield 'month names' => ['0 0 1 jan,oct *', '2026-10-01 00:00', true];
    }

    #[DataProvider('schedules')]
    public function testMatch(string $schedule, string $now, bool $expected): void
    {
        $this->assertSame($expected, $this->schedulerAt($now)->match($schedule));
    }

    public function testInvalidSchedule(): void
    {
        $this->expectException(CronException::class);
        $this->schedulerAt('2026-10-19 14:00')->match('0 */0 * * *');
    }
}
