<?php

/**
 * Created on Sat Apr 24 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Crontab;

use DateTimeZone;

class DateTimeFactory implements DateTimeFactoryInterface
{
    /**
     *
     * @var \DateTimeZone
     */
    private $timezone;

    public function __construct($timezone)
    {
        $this->timezone = new DateTimeZone($timezone);
    }

    /**
     * Return Datetime according to configured Timezone
     *
     * @param string|null $date
     * @return void
     */
    public function createDateTime(?string $date = null): \DateTime
    {
        return  new \DateTime($date, $this->timezone);
    }
}
