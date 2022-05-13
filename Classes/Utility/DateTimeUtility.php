<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Utility;

class DateTimeUtility
{
    public static function getTimestamp(): int
    {
        return time() + (int)date('Z');
    }
}
