<?php

/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/

namespace Nicoren\CronBundle\DependencyInjection\Compiler;

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
        foreach ($container->findTaggedServiceIds('nicoren_cron.storage_adapter') as $id => $tags) {
            $adapters[$tags[0]["alias"]] = new Reference($id);
            if ($container->getParameter('nicoren_cron.storage.adapter_code') == $tags[0]["alias"]) {

                $container->setAlias('nicoren_cron.storage.adapter', new Alias($id, false));
            }
        }
        $pool->setArgument(0, $adapters);
    }
}
