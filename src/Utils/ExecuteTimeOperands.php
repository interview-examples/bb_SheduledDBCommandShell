<?php

namespace App\Utils;

class ExecuteTimeOperands
{
    public static function validateTime(string $executeAt): string {
        try {
            $dateTime = Carbon::parse($executeAt);
            if (!$dateTime || $dateTime->isFalse()) {
                throw new InvalidArgumentException("Invalid date/time format: $executeAt");
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
}