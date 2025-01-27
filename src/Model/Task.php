<?php

namespace App\Model;

use App\Utils\CommandOperands;
use App\Utils\ExecuteTimeOperands;
use App\Utils\InputSanitizer;
use RuntimeException;

class Task
{
    private int $id = -1;
    private string $command;
    private string $description;
    private string $executeAt;
    private string $status;

    public function __construct(string $command,
                                string $description,
                                string $executeAt,
                                string $status)
    {
        $this->setCommand($command);
        $this->setDescription($description);
        $this->setExecuteAt($executeAt);
        $this->setStatus($status);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    /**
     * @throws RuntimeException
     */
    public function setId(int $id): void {
        $backtrace = debug_backtrace();
        $allowedCallers = [
            'App\Repository\TaskRepository::addTask',
            'App\Repository\TaskRepository::findTasksPaginated',
            'App\Repository\TaskRepository::fetchResultToTasksObject',
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
        $this->command = CommandOperands::validateCommand($command);
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

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string {
        return $this->status;
    }
}
