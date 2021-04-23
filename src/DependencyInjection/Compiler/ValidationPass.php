<?php
/*
 * Created on Mon Apr 19 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register validation files
 */
class ValidationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('validator.builder')) {
            $container
                ->getDefinition('validator.builder')
                ->addMethodCall('addXmlMappings', [[__DIR__ . '/../../Resources/config/validation.xml']]);
        }
    }
}
