<?php

namespace App\Model;

use App\Service\TaskDataService;
use App\Utils\ExecuteTimeOperands;
use App\Utils\InputSanitizer;
use RuntimeException;

class Task
{
    private TaskDataService $taskDataService;

    private int $id;
    private string $command;
    private string $description;
    private string $executeAt;

    public function __construct(string $command,
                                string $description,
                                string $executeAt,
                                \App\Service\TaskDataService $taskDataService)
    {
        $this->taskDataService = $taskDataService;
        $this->setCommand($command);
        $this->setDescription($description);
        $this->setExecuteAt($executeAt);
    }

    public function getId(): int {
        return $this->id;
    }

    /**
     * @throws RuntimeException
     */
    public function setId(int $id): void {
        $backtrace = debug_backtrace();
        $allowedCallers = [
            'App\Repository\TaskRepository::create',
        ];

        $caller = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];

        if (!in_array($caller, $allowedCallers, true)) {
            throw new RuntimeException("Invalid calling method 'setId' from '{$caller}'");
        }
        $this->id = $id;
    }

    public function getCommand(): string {
        return $this->command;
    }
    public function setCommand(string $command): void {
        $this->command = $this->taskDataService->validateCommand($command);
    }

    public function getDescription(): string {
        return $this->description;
    }
    public function setDescription(string $description): void {
        $this->description = InputSanitizer::cleanString($description);
    }

    public function getExecuteAt(): string {
        return $this->executeAt;
    }
    public function setExecuteAt(string $executeAt): void {
        $this->executeAt = ExecuteTimeOperands::validateTime(InputSanitizer::cleanString($executeAt));
    }

    public function getExecuteAtCron(): string {
        return ExecuteTimeOperands::convertDatetimeToCronFormat($this->executeAt);
    }
}