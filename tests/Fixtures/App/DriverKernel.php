<?php

namespace Nicoren\CronBundle\Tests\Fixtures\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Nicoren\CronBundle\NicorenCronBundle;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Kernel with the bundle configured for one doctrine driver (orm or mongodb)
 */
class DriverKernel extends Kernel
{
    public function __construct(private string $driver, private string $mongoDbUrl = '')
    {
        parent::__construct('test_' . $driver, true);
    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield 'orm' === $this->driver ? new DoctrineBundle() : new DoctrineMongoDBBundle();
        yield new NicorenCronBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/NicorenCronBundle/driver/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/NicorenCronBundle/driver/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'secret' => 'test',
                'test' => true,
                'http_method_override' => false,
                'handle_all_throwables' => true,
                'php_errors' => ['log' => true],
            ]);
            if ('orm' === $this->driver) {
                $container->loadFromExtension('doctrine', [
                    'dbal' => ['driver' => 'pdo_sqlite', 'memory' => true],
                    'orm' => [],
                ]);
            } else {
                $container->loadFromExtension('doctrine_mongodb', [
                    'connections' => ['default' => ['server' => $this->mongoDbUrl]],
                    'default_database' => 'nicoren_cron_bundle_test',
                    'document_managers' => ['default' => []],
                ]);
            }
            $container->loadFromExtension('nicoren_cron', ['db_driver' => $this->driver]);
            $container->register('logger', NullLogger::class);
        });
    }

    protected function build(ContainerBuilder $container): void
    {
        // expose the bundle services to the tests
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                foreach (['nicoren_cron.job_manager', 'nicoren_cron.object_manager'] as $id) {
                    ($container->hasAlias($id) ? $container->getAlias($id) : $container->getDefinition($id))->setPublic(true);
                }
            }
        });
    }
}
