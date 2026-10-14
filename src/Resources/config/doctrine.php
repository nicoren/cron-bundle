<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\Persistence\ObjectManager;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('nicoren_cron.object_manager', ObjectManager::class)
        ->private()
        ->factory([service('nicoren_cron.doctrine.objectmanager_provider'), 'getObjectManager']);
};
