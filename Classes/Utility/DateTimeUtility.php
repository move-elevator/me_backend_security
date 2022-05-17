<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Utility;

use DateInterval;
use DateTime;

class DateTimeUtility
{
    public static function getTimestamp(): int
    {
        return time() + (int)date('Z');
    }

    public static function isDeadlineReached(int $deadlineEndInDays, int $lastUpdateTimestamp): bool
    {
        $now = new DateTime();
        $deadline = new DateTime();
        $deadline->setTimestamp($lastUpdateTimestamp);
        $deadline->add(
            new DateInterval('P' . $deadlineEndInDays . 'D')
        );

        return $now > $deadline;
    }
}
