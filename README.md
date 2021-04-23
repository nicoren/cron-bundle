# cron-bundle
Cron is schedule integration for symfony.
===========

 [![Packagist](https://img.shields.io/packagist/v/nicoren/cron-bundle.svg?style=flat-square)](https://packagist.org/packages/nicoren/cron-bundle)
 [![Build Status](https://img.shields.io/travis/Nicoren/Cron-Bundle.svg?style=flat-square)](https://travis-ci.org/Nicoren/Cron-Bundle)
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
composer require nicoren/cron-bundle
```
without symfony Flex :
```shell
composer require nicoren/cron-bundle
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

Available commands
------------------

### list
```shell
bin/console cron:job:list
```
Show a list of all jobs.

### create
```shell
bin/console cron:job:create
```
Create a new job.

### delete
```shell
bin/console cron:job:delete _jobId_
```
Delete a job. For your own protection, the job must be disabled first.

### enable
```shell
bin/console cron:job:enable _jobId_
```
Enable a job.

### disable
```shell
bin/console cron:job:disable _jobId_
```
Disable a job.

### run all jobs schelduled at current time
```shell
bin/console cron:run
```
> which we borrowed from Symfony. 
> Make sure to check out [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer) as this will help you a lot.  
> Please note that `--force` forces the job to be executed (even if disabled) based on the job schedule  

Requirements
------------

PHP 7.3 or above

Author and contributors
-----------------------

Nicolas RENAULT - <nicoren44@gmail.com>


License
-------

CronBundle is licensed under the MIT license.
