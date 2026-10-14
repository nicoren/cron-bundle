<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nicoren\CronBundle\Command\CreateCronCommand;
use Nicoren\CronBundle\Command\DeleteCronCommand;
use Nicoren\CronBundle\Command\DisableCronCommand;
use Nicoren\CronBundle\Command\EnableCronCommand;
use Nicoren\CronBundle\Command\ListCronCommand;
use Nicoren\CronBundle\Command\RunCronCommand;
use Nicoren\CronBundle\Command\RunJobCommand;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services->set('nicoren_cron.command.create_cron', CreateCronCommand::class)
        ->tag('console.command', ['command' => 'cron:job:create']);
    $services->set('nicoren_cron.command.run_cron', RunCronCommand::class)
        ->tag('console.command', ['command' => 'cron:run']);
    $services->set('nicoren_cron.command.run_job', RunJobCommand::class)
        ->tag('console.command', ['command' => 'cron:job:run']);
    $services->set('nicoren_cron.command.list_cron', ListCronCommand::class)
        ->tag('console.command', ['command' => 'cron:job:list']);
    $services->set('nicoren_cron.command.delete_cron', DeleteCronCommand::class)
        ->tag('console.command', ['command' => 'cron:job:delete']);
    $services->set('nicoren_cron.command.enable_cron', EnableCronCommand::class)
        ->tag('console.command', ['command' => 'cron:job:enable']);
    $services->set('nicoren_cron.command.disable_cron', DisableCronCommand::class)
        ->tag('console.command', ['command' => 'cron:job:disable']);
};
