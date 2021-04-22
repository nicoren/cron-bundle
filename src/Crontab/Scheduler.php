<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @license MIT
 */

namespace Nicoren\CronBundle\Crontab;

use Nicoren\CronBundle\Exception\CronException;

class Scheduler implements SchedulerInterface
{
    private const PATTERN_DEFAULT = '#\s+#';


    /**
     * {@inheritdoc}
     */
    public function match(string $value): bool
    {
        $exprArr = $this->explodeCronExpr($value);
        return $this->matchCronExpr($exprArr);
    }



    /**
     * Checks the observer's cron expression against time.
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @return bool
     */
    protected function matchCronExpr(array $exprArr)
    {

        if (count($exprArr) !== 5) {
            throw new CronException(sprintf('Invalid cron expression: %s', implode(" ", $exprArr)));
        }
        $match = true;
        $currentTime = new \DateTime();
        $time = [
            $currentTime->format('i') + $currentTime->format('H') * 60,
            $currentTime->format('H'),
            $currentTime->format('d'),
            $currentTime->format('m'),
            $currentTime->format('w')
        ];
        for ($i = 0; $i < count($exprArr); $i++) {
            $match = $match && $this->matchCronExpression($exprArr[$i], $time[$i]);
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
    protected function explodeCronExpr($expr): array
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
     * @param int $num
     * @return bool
     * @throws CronException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function matchCronExpression($expr, int $num): bool
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr, ',') !== false) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e, $num)) {
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
            $num = $num % 60;
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
        return $num >= $from && $num <= $to && $num % $mod === 0;
    }

    /**
     * Get number of a month.
     *
     * @param int|string $value
     * @return bool|int|string
     */
    protected function getNumeric($value)
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
