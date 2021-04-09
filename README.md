# cron-bundle
Cron is schedule integration for symfony.
===========

 [![Packagist](https://img.shields.io/packagist/v/nicoren/cron-bundle.svg?style=flat-square)](https://packagist.org/packages/nicoren/cron-bundle)
 [![Build Status](https://img.shields.io/travis/Nicoren/Symfony-Bundle.svg?style=flat-square)](https://travis-ci.org/Nicoren/Symfony-Bundle)
 [![Packagist](https://img.shields.io/packagist/dt/Nicoren/Cron-Bundle.svg?style=flat-square)](https://packagist.org/packages/nicoren/cron-bundle)
 [![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
 
[Cron](https://github.com/Cron/Cron) integration for symfony.

Installation
------------
Installing this bundle can be done through these simple steps:

1. Add the bundle to your project as a composer dependency:
With symfony Flex :

```shell
composer config extra.symfony.allow-contrib true
composer require cron/cron-bundle
```
without symfony Flex :
```shell
composer require cron/cron-bundle
```
2. Add the bundle to your application kernel :
 **If you don't use Symfony flex**
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Nicoren\CronBundle\CronCronBundle(),
    ];

    // ...
}
```
3. Update your DB schema

3.1. ( doctrine ORM)
```shell
bin/console make:migration
bin/console doctrine:migrations:migrate
```

3.2. ( doctrine odm)
```shell
doctrine:mongodb:schema:update
```

4. Start using the bundle:
```shell
bin/console cron:list
bin/console cron:run
```

5. To run your cron jobs automatically, add the following line to your (or whomever's) crontab:
```
* * * * * /path/to/symfony/install/app/console cron:run 1>> /dev/null 2>&1
```
  **OR**
  If you don't have a dedicated cron daemon (e.g. in Heroku), you can use:
```shell
bin/console cron:start # will run in background mode, use --blocking to run in foreground
bin/console cron:stop # will stop the background cron daemon
```

Available commands
------------------

### list
```shell
bin/console cron:list
```
Show a list of all jobs. Job names are show with ```[x]``` if they are enabled and ```[ ]``` otherwise.

### create
```shell
bin/console cron:create
```
Create a new job.

### delete
```shell
bin/console cron:delete _jobName_
```
Delete a job. For your own protection, the job must be disabled first.

### enable
```shell
bin/console cron:enable _jobName_
```
Enable a job.

### disable
```shell
bin/console cron:disable _jobName_
```
Disable a job.

### run
```shell
bin/console cron:run [--force] [job]
```
> which we borrowed from Symfony. 
> Make sure to check out [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer) as this will help you a lot.  
> Please note that `--force` forces the job to be executed (even if disabled) based on the job schedule  

### run now, independent of the job schedule
```shell
bin/console cron:run --schedule_now [--force] job
```

### start
```shell
bin/console cron:start [--blocking]
```
Start the cron as a daemon. By default it forks itself to the background and suppresses any output. The `--blocking` option will keep it in the foreground and will display output. This is useful when you don't have a dedicated cron daemon (e.g. on Heroku).

### stop
```shell
bin/console cron:stop
```
Stops the background cron daemon started with `cron:start`. This is not applicable when the daemon was started with `--blocking`.

Requirements
------------

PHP 7.3 or above

Author and contributors
-----------------------

Nicolas RENAULT - <nicoren44@gmail.com>


License
-------

CronBundle is licensed under the MIT license.
