<?php
/*
 * Created on Tue Apr 13 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

namespace Nicoren\CronBundle\Doctrine;

class Drivers
{
    const DRIVER_ORM = "orm";
    const DIVER_MONGODB = "mongodb";

    public static function getDrivers()
    {
        return [static::DRIVER_ORM, static::DIVER_MONGODB];
    }
}
