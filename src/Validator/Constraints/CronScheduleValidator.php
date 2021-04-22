<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
