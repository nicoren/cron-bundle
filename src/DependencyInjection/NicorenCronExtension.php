<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\DependencyInjection;

use Nicoren\CronBundle\DependencyInjection\Configuration\RedisConfiguration;
use Nicoren\CronBundle\Doctrine\Drivers;
use Nicoren\CronBundle\Model\Job;
use Nicoren\CronBundle\Model\JobInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NicorenCronExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private static $doctrineDrivers = [
        Drivers::DRIVER_ORM => [
            'registry' => 'doctrine',
            'tag' => 'doctrine.event_subscriber',
        ],
        Drivers::DIVER_MONGODB => [
            'registry' => 'doctrine_mongodb',
            'tag' => 'doctrine_mongodb.odm.event_subscriber',
        ]
    ];


    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $dbDrivers = array_column($container->getExtensionConfig($this->getAlias()), 'db_driver');
        if (in_array(Drivers::DRIVER_ORM, $dbDrivers, true) && $container->hasExtension('doctrine')) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => ['resolve_target_entities' => [JobInterface::class => Job::class]],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        foreach (['services', 'commands'] as $basename) {
            $loader->load(sprintf('%s.php', $basename));
        }


        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $loader->load('doctrine.php');
            $loader->load(sprintf('doctrine-%s.php', $config['db_driver']));
        } else {
            $loader->load(sprintf('%s.php', $config['db_driver']));
        }
        $container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);

        $container->setAlias('nicoren_cron.job_manager', new Alias($config['service']['job_manager'], true));
        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'db_driver' => 'nicoren_cron.storage',
                'model_manager_name' => 'nicoren_cron.model_manager_name',
                'job_class' => 'nicoren_cron.model.job.class',
                'timezone' => 'nicoren_cron.timezone',
                'process_timeout' => 'nicoren_cron.process_timeout',
            ],
        ]);

        $container->setParameter('nicoren_cron.storage.adapter_code', $config['storage']['adapter']);
        if ($config['storage']['adapter'] == "redis" && isset($config['storage']["redis"]) && isset($config['storage']["redis"]["type"])) {
            $container->setParameter("nicoren_cron.redis.client_code", $config['storage']["redis"]["type"]);
            $dsn = $config['storage']["redis"]["dsn"];
            $a = [];
            $container->resolveEnvPlaceholders($dsn, null, $a);
            $redisConfigDefinition = new Definition(RedisConfiguration::class, [$dsn, count($a) > 0]);
            if (isset($config['storage']["redis"]["parameters"]) && isset($config['storage']["redis"]["parameters"]["database"])) {
                $redisConfigDefinition->addMethodCall('setDatabase', [$config['storage']["redis"]["parameters"]["database"]]);
            }
            $container->setDefinition("nicoren_cron.adapter.redis.configuration", $redisConfigDefinition);
        }
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map): void
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces): void
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
