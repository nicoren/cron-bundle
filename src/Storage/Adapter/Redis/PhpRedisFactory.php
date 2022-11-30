<?php

/**
 * Created on Thu Mar 17 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/

namespace Nicoren\CronBundle\Storage\Adapter\Redis;

use Nicoren\CronBundle\DependencyInjection\Configuration\RedisConfiguration;

class PhpRedisFactory
{
    public static function create(RedisConfiguration $redisConfiguration, string $prefix)
    {
        $client = new \Redis();
        $auth = null;
        $options = [];
        if ($redisConfiguration->getUsername() && $redisConfiguration->getPassword()) {
            $options = ['auth' => [$redisConfiguration->getUsername(), $redisConfiguration->getPassword()]];
        }
        $client->connect($redisConfiguration->getHost(), $redisConfiguration->getPort(), 0, NULL, 0, 0, $options);
        $client->select($redisConfiguration->getDatabase());
        return new PhpRedis($prefix, $client);
    }
}
