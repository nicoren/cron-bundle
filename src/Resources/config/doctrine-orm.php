<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nicoren\CronBundle\Doctrine\ObjectManagerProvider;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('nicoren_cron.doctrine.objectmanager_provider', ObjectManagerProvider::class)
        ->private()
        ->args([
            service('doctrine'),
            param('nicoren_cron.model_manager_name'),
        ]);
};
