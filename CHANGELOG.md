# Changelog

## 2.0.0

Compatibility with Symfony 7.4 / 8 and PHP 8.2+.

### Breaking changes

- PHP 8.2 or above, Symfony 7.4 or 8 (Symfony 4 and 5 are not supported anymore).
- `doctrine/doctrine-bundle` and `doctrine/mongodb-odm-bundle` are not required anymore:
  install the one matching your `db_driver` (`orm` or `mongodb`).
- `symfony/cache` is not required anymore (it was not used).
- The services are defined in PHP instead of XML (`Resources/config/*.php`): XML service configuration
  is deprecated since Symfony 7.4 and removed in Symfony 8. Service ids, aliases and tags are unchanged.
- `CronSchedule` constraint: options are named arguments (`new CronSchedule(message: '...')`),
  it can also be used as an attribute (`#[CronSchedule]`).

### Doctrine ORM

The ORM mapping of `Job` never worked (no identifier). It is fixed : the jobs are stored in a `jobs` table
(`id`, `name`, `command`, `description`, `schedule`, `enabled`, `created_at`, `max_concurrent`).
Create it with your migrations (`bin/console make:migration`) or `bin/console doctrine:schema:update --force`.

### Configuration

- `job_class` and `timezone` are optional (defaults: `Nicoren\CronBundle\Model\Job` and `UTC`).
- The default `storage.adapter` is `filesystem` (the previous default value matched no adapter).

### Fixes

- Filesystem storage adapter: the running pids were never saved.
- Redis storage adapter: `storage.redis.parameters.database` was ignored.
- The container could not be built without a redis configuration.
- `cron:job:delete|enable|disable|run`: the `id` argument was declared with a wrong mode.
- `cron:job:create`: `--enabled=false` enabled the job.
- PHP 8 deprecations (null passed to internal functions).

## 1.0.9

Last version supporting Symfony 4.4 / 5 and PHP 7.3.
