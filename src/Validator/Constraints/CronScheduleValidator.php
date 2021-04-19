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

use Nicoren\CronBundle\Exception\CronException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CronScheduleValidator extends ConstraintValidator
{

    private const PATTERN_DEFAULT = '#\s+#';


    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $exprArr = $this->parseCronExpr($value);
        $this->validateSchedule($exprArr);
    }



    /**
     * Checks the observer's cron expression against time.
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @return bool
     */
    public function validateSchedule(array $exprArr)
    {

        if (count($exprArr) !== 5) {
            return false;
        }
        $match = true;
        for ($i = 0; $i < count($exprArr); $i++) {
            $match = $match && $this->matchCronExpression($exprArr[$i], $i);
        }
        return $match;
    }


    /**
     * Set cron expression.
     *
     * @param string $expr
     * @return string[]
     * @throws CronException
     */
    public function parseCronExpr($expr): array
    {
        $e = preg_split(static::PATTERN_DEFAULT, $expr, null, PREG_SPLIT_NO_EMPTY);
        if (sizeof($e) < 5 || sizeof($e) > 6) {
            throw new CronException(sprintf('Invalid cron expression: %s', $expr));
        }

        return $e;
    }


    /**
     * Match cron expression.
     *
     * @param string $expr
     * @param int $index
     * @return bool
     * @throws CronException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function matchCronExpression($expr, int $index): bool
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr, ',') !== false) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e, $index)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (strpos($expr, '/') !== false) {
            $e = explode('/', $expr);
            if (sizeof($e) !== 2) {
                throw new CronException(sprintf('Invalid cron expression, expecting \'match/modulus\': %s', $expr));
            }
            if (!is_numeric($e[1])) {
                throw new CronException(sprintf('Invalid cron expression, expecting numeric modulus: %s', $expr));
            }
            $expr = $e[0];
            $mod = $e[1];
        } else {
            $mod = 1;
        }

        // handle all match by modulus
        if ($expr === '*') {
            $from = 0;
            $to = 60;
        } elseif (strpos($expr, '-') !== false) {
            // handle range
            $e = explode('-', $expr);
            if (sizeof($e) !== 2) {
                throw new CronException(sprintf('Invalid cron expression, expecting \'from-to\' structure: %s', $expr));
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);
        } else {
            // handle regular token
            $from = $this->getNumeric($expr);
            $to = $from;
        }

        if ($from === false || $to === false) {
            throw new CronException(sprintf('Invalid cron expression: %s', $expr));
        }
        return $index >= $from && $index <= $to && $index % $mod === 0;
    }

    /**
     * Get number of a month.
     *
     * @param int|string $value
     * @return bool|int|string
     */
    public function getNumeric($value)
    {
        static $data = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'aug' => 8,
            'sep' => 9,
            'oct' => 10,
            'nov' => 11,
            'dec' => 12,
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }
        return false;
    }
}
