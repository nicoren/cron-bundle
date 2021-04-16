<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Nicoren\CronBundle\Doctrine\Drivers;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NicorenCronExtension extends Extension
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
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $loader->load('doctrine.xml');
            $container->setAlias('nicoren_cron.doctrine_registry', new Alias(self::$doctrineDrivers[$config['db_driver']]['registry'], false));
        } else {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        }
        $container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);

        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $definition = $container->getDefinition('nicoren_cron.object_manager');
            $definition->setFactory([new Reference('nicoren_cron.doctrine_registry'), 'getManager']);
        }

        foreach (['listeners', 'commands'] as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
        $container->setAlias('nicoren_cron.job_manager', new Alias($config['service']['job_manager'], true));

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'db_driver' => 'nicoren_cron.storage',
                'model_manager_name' => 'nicoren_cron.model_manager_name',
                'job_class' => 'nicoren_cron.model.job.class',
            ],
        ]);
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
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
