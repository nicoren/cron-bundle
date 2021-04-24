<?php

/**
 * Created on Sat Apr 24 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Crontab;


interface DateTimeFactoryInterface
{

    /**
     * Return Datetime according to configured Timezone
     *
     * @param string|null $date
     * @return void
     */
    public function createDateTime(?string $date = null): \DateTime;
}
