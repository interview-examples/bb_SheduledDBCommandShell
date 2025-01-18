<?php

namespace App\Utils;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use DateInterval;
use Exception;
use InvalidArgumentException;

class ExecuteTimeOperands
{

    /**
     * Validates a given string as a valid date and time in the future.
     *
     * The given string can be in the following formats:
     * - "YYYY-MM-DD HH:MM:SS"
     * - "+15m", "+1h", "+1h30m", "+1h30m15s"
     *
     * If the given string is not a valid date and time, an exception is thrown.
     *
     * @param string $executeAt The string to validate.
     * @return string The validated date and time in the format "YYYY-MM-DD HH:MM:SS".
     * @throws InvalidArgumentException If the given string is not a valid date and time.
     */
    public static function validateTime(string $executeAt): string {
        global $config;
        try {
            $timezone = new CarbonTimeZone('Asia/Jerusalem');
            $timezone->toRegionTimeZone();
            if (preg_match('/^\+(\d+h)?(\d+m)?(\d+s)?$/', $executeAt)) {
                $dateTime = Carbon::now();
                $dateTime->setTimezone('Asia/Jerusalem');
                $interval = self::parseInterval($executeAt);
                $dateTime->add($interval);
            } else {
                $dateTime = Carbon::parse($executeAt);
            }
            if (is_null($dateTime)) {
                throw new InvalidArgumentException("Invalid date/time format: $executeAt");
            }

            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            throw new InvalidArgumentException("Invalid date/time format: $executeAt");
        }
    }

    /**
     * Converts a given date and time string to a cron format.
     *
     * The given string must be in the format "YYYY-MM-DD HH:MM:SS".
     *
     * @param string $executeAt The date and time string to convert.
     * @return string The converted cron format string.
     * @throws InvalidArgumentException If the given string is not a valid date and time.
     */
    public static function convertDatetimeToCronFormat(string $executeAt): string {
        try {
            $dateTime = Carbon::parse($executeAt);
            return sprintf('%d %d %d %d *',
                $dateTime->minute,
                $dateTime->hour,
                $dateTime->day,
                $dateTime->month
            );
        } catch (Exception $e) {
            throw new InvalidArgumentException("Invalid date/time format for cron: $executeAt");
        }
    }

    /**
     * Parse an interval string given as "+XdYmZs" where X, Y, and Z are integers and
     * d, m, and s are days, months, and seconds respectively.
     *
     * @param string $intervalString The interval string to parse.
     * @return DateInterval The parsed interval.
     * @throws InvalidArgumentException If the given string is not a valid interval.
     * @throws \DateMalformedIntervalStringException
     */
    private static function parseInterval(string $intervalString): DateInterval
    {
        $intervalString = ltrim($intervalString, '+');
        $parts = [
            'h' => 0,
            'm' => 0,
            's' => 0,
        ];
        if (preg_match_all('/(\d+)([hms])/', $intervalString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $parts[$match[2]] = (int)$match[1];
            }
        } else {
            throw new InvalidArgumentException("Invalid interval format: $intervalString");
        }

        return new DateInterval(sprintf('PT%dH%dM%dS', $parts['h'], $parts['m'], $parts['s']));
    }
}