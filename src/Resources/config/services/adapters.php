<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nicoren\CronBundle\Storage\Adapter\Filesystem;
use Nicoren\CronBundle\Storage\Adapter\Pool;
use Nicoren\CronBundle\Storage\Adapter\PoolInterface;
use Nicoren\CronBundle\Storage\Adapter\Redis;
use Nicoren\CronBundle\Storage\Adapter\Redis\PhpRedis;
use Nicoren\CronBundle\Storage\Adapter\Redis\PhpRedisFactory;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    // Pool adapter
    $services->set('nicoren_cron.storage.adapter.pool', Pool::class)
        ->args([[]]);
    $services->alias(PoolInterface::class, 'nicoren_cron.storage.adapter.pool');

    // Adapters
    $services->set('nicoren_cron.storage.adapter.filesystem', Filesystem::class)
        ->args([param('kernel.project_dir') . '/var/cron/pids'])
        ->tag('nicoren_cron.storage_adapter', ['alias' => 'filesystem']);

    $services->set('nicoren_cron.storage.adapter.redis', Redis::class)
        ->tag('nicoren_cron.storage_adapter', ['alias' => 'redis']);

    $services->set('nicoren_cron.redis.adapter.phpredis', PhpRedis::class)
        ->lazy()
        ->factory([PhpRedisFactory::class, 'create'])
        ->args([
            service('nicoren_cron.adapter.redis.configuration'),
            'nicoren_cron_pids_',
        ])
        ->tag('nicoren_cron.redis_adapter', ['alias' => 'PhpRedis']);
};
