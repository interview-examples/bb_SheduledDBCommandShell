<?php

namespace App\Utils;

class CommandOperands
{
    /**
     * Парсит и валидирует команду CLI.
     *
     * @param string $timeString Временной интервал (например, "+15m")
     * @param string $command Команда для выполнения
     * @param string $description Описание задачи
     * @return array Ассоциативный массив с валидированной командой, временем выполнения и описанием
     * @throws InvalidArgumentException Если формат неверный
     */
    public static function parseAndValidateCommand(
        string $timeString,
        string $command,
        string $description): array
    {
        $executeAt = ExecuteTimeOperands::validateTime($timeString);
        $validatedCommand = self::validateCommand($command);

        return [
            'executeAt' => $executeAt,
            'command' => $validatedCommand,
            'description' => $description,
        ];
    }

    /**
     * Проверяет и валидирует команду.
     *
     * @param string $command Входная команда
     * @return string Валидированная команда
     * @throws InvalidArgumentException Если команда неверная
     */
    public static function validateCommand(string $command): string {
        $allowedCommands = ["write to db", "send email", "write to screen"];
        $command = strtolower(InputSanitizer::cleanString($command));
        if (!in_array($command, $allowedCommands, true)) {
            throw new InvalidArgumentException("Invalid command: $command");
        }
        return $command;
    }
}