<?php

/**
 * Created on Sat Apr 17 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class CronSchedule extends Constraint
{
    public $message = 'This value has not crontab schedule format.';
}
