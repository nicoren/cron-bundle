<?php

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/

namespace Nicoren\CronBundle\DependencyInjection\Compiler;

use Nicoren\CronBundle\DependencyInjection\Configuration\RedisDsnParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Alias;

class StorageAdapterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $pool = $container->getDefinition('nicoren_cron.storage.adapter.pool');

        $adapters = [];
        $storageAdapterId = null;
        foreach ($container->findTaggedServiceIds('nicoren_cron.storage_adapter') as $id => $tags) {
            $adapters[$tags[0]["alias"]] = new Reference($id);
            if ($container->getParameter('nicoren_cron.storage.adapter_code') == $tags[0]["alias"]) {
                $storageAdapterId = $id;
                $container->setAlias('nicoren_cron.storage.adapter', new Alias($id, false));
            }
        }

        foreach ($container->findTaggedServiceIds('nicoren_cron.redis_adapter') as $id => $tags) {
            $adapters[$tags[0]["alias"]] = new Reference($id);
            if ($container->getParameter('nicoren_cron.redis.client_code') == $tags[0]["alias"]) {
                $container->setAlias('nicoren_cron.redis.adapter', new Alias($id, false));
            }
        }

        $pool->setArgument(0, $adapters);

        $redisDef = $container->getDefinition('nicoren_cron.storage.adapter.redis');
        if ($redisDef) {
            $tags = $redisDef->getTags();
            if (isset($tags["nicoren_cron.storage_adapter"]) && isset($tags["nicoren_cron.storage_adapter"][0]) && isset($tags["nicoren_cron.storage_adapter"][0]["alias"])) {
                $redisAlias = $tags["nicoren_cron.storage_adapter"][0]["alias"];
                if ($container->getParameter('nicoren_cron.storage.adapter_code') == $redisAlias) {
                    $redis = $container->getDefinition($storageAdapterId);
                    $redis->setArgument(0, new Reference("nicoren_cron.redis.adapter"));
                }
            }
        }
    }
}
