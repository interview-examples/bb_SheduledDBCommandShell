<?php

namespace App\Utils;

use Carbon\Carbon;
use \InvalidArgumentException;

class ExecuteTimeOperands
{
    public static function validateTime(string $executeAt): string {
        try {
            if (preg_match('/^\+(\d+h)?(\d+m)?(\d+s)?$/', $executeAt)) {
                $dateTime = Carbon::now();
                $interval = self::parseInterval($executeAt);
                $dateTime->add($interval);
            } else {
                $dateTime = Carbon::parse($executeAt);
            }
            if (!$dateTime || $dateTime->isFalse()) {
                throw new InvalidArgumentException("Invalid date/time format: $executeAt");
            }
            if ($dateTime->isPast()) {
                throw new InvalidArgumentException("Date/time cannot be in the past: $executeAt");
            }
            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Invalid date/time format: $executeAt");
        }
    }

    public static function convertDatetimeToCronFormat(string $executeAt): string {
        try {
            $dateTime = Carbon::parse($executeAt);
            return sprintf('%d %d %d %d *',
                $dateTime->minute,
                $dateTime->hour,
                $dateTime->day,
                $dateTime->month
            );
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Invalid date/time format for cron: $executeAt");
        }
    }

    /**
     * Разбирает строку интервала и возвращает CarbonInterval.
     *
     * @param string $intervalString Входная строка интервала (например, "+1h23m")
     * @return \DateInterval
     * @throws InvalidArgumentException Если формат неверный
     */
    private static function parseInterval(string $intervalString): \DateInterval
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

        return new \DateInterval(sprintf('PT%dH%dM%dS', $parts['h'], $parts['m'], $parts['s']));
    }
}