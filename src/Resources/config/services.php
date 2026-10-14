<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nicoren\CronBundle\Crontab\DateTimeFactory;
use Nicoren\CronBundle\Crontab\DateTimeFactoryInterface;
use Nicoren\CronBundle\Crontab\Runner;
use Nicoren\CronBundle\Crontab\RunnerInterface;
use Nicoren\CronBundle\Crontab\Scheduler;
use Nicoren\CronBundle\Crontab\SchedulerInterface;
use Nicoren\CronBundle\Doctrine\JobManager;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Nicoren\CronBundle\Validator\Constraints\CronScheduleValidator;

return static function (ContainerConfigurator $container): void {
    $container->import('services/*.php');

    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    // Job manager service
    $services->set('nicoren_cron.job_manager.default', JobManager::class)
        ->args([
            service('nicoren_cron.object_manager'),
            param('nicoren_cron.model.job.class'),
        ]);
    $services->alias(JobManagerInterface::class, 'nicoren_cron.job_manager.default');

    // Crontab runner service
    $services->set('nicoren_cron.crontab_runner.default', Runner::class)
        ->arg('$adapter', service('nicoren_cron.storage.adapter'));
    $services->alias(RunnerInterface::class, 'nicoren_cron.crontab_runner.default');

    // Crontab scheduler
    $services->set('nicoren_cron.crontab_scheduler.default', Scheduler::class);
    $services->alias(SchedulerInterface::class, 'nicoren_cron.crontab_scheduler.default');
    $services->set(CronScheduleValidator::class)
        ->public();

    $services->set('nicoren_cron.crontab_datetime_factory.default', DateTimeFactory::class)
        ->args([param('nicoren_cron.timezone')]);
    $services->alias(DateTimeFactoryInterface::class, 'nicoren_cron.crontab_datetime_factory.default');
};
