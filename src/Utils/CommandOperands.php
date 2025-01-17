<?php

namespace App\Utils;

use InvalidArgumentException;

class CommandOperands
{
    public static function validateCommand(string $command): string {
        $allowedCommands = ["write to db", "send email", "out to screen"];
        $command = strtolower(InputSanitizer::cleanString($command));
        if (!in_array($command, $allowedCommands, true)) {
            throw new InvalidArgumentException("Invalid command: $command");
        }
        return $command;
    }

}