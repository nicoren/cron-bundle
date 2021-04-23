<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */


namespace Nicoren\CronBundle\DependencyInjection;

use Nicoren\CronBundle\Doctrine\Drivers;
use Nicoren\CronBundle\Model\Job;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Created on Sat Apr 24 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

class Configuration implements ConfigurationInterface
{
    /**
     * Returns the config tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $supportedDrivers = Drivers::getDrivers();
        $treeBuilder = new TreeBuilder('nicoren_cron');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->scalarNode('db_driver')
            ->validate()
            ->ifNotInArray($supportedDrivers)
            ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($supportedDrivers))
            ->end()
            ->cannotBeOverwritten()
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('job_class')->isRequired()->defaultValue(Job::class)->end()
            ->scalarNode('model_manager_name')->defaultNull()->end();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('service')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('job_manager')->defaultValue('nicoren_cron.job_manager.default')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
