<?php

/**
 * Created on Sat Apr 17 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Validator\Constraints;

use Nicoren\CronBundle\Crontab\SchedulerInterface;
use Nicoren\CronBundle\Exception\CronException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CronScheduleValidator extends ConstraintValidator
{
    /**
     * @var SchedulerInterface
     */
    protected SchedulerInterface $scheduler;

    public function __construct(SchedulerInterface $scheduler)
    {
        $this->scheduler = $scheduler;
    }


    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $this->scheduler->match($value);
    }
}
