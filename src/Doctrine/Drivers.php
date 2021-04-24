<?php

/**
 * Created on Mon Apr 19 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
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
