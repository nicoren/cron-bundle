<?php

namespace Nicoren\CronBundle\Tests\Fixtures\App\App;

use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppKernel extends Kernel
{

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {

        $contents = require __DIR__ . '/config/bundles.php';
        $array = [];
        foreach ($contents as $class => $envs) {

            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                $array[] = new $class();
            }
        }
        return $array;
    }

    public function getRootDir()
    {
        return __DIR__;
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/services/base.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/NicorenCronBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir() . '/NicorenCronBundle/logs';
    }

    protected function build(ContainerBuilder $container)
    {
        $container->register('logger', NullLogger::class);

        if (!$container->hasParameter('kernel.root_dir')) {
            $container->setParameter('kernel.root_dir', $this->getRootDir());
        }
    }
}
