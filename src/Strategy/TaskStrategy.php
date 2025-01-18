<?php

namespace App\Strategy;

use App\Model\Task;
use App\Repository\TaskRepository;

class TaskStrategy implements TaskStrategyInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
    }

    public function execute(Task $task): void
    {
        switch ($task->getCommand()) {
            case 'write to db':
                $this->taskRepository->logExecutedTask($task->getDescription());    // ToDo method
                break;
            case 'send email':
                mail('vit.trakhtenberg@gmail.com', 'Scheduled Task', $task->getDescription());  // FixMe change email after
                break;
            case 'out to screen':
                echo $task->getDescription() . "\n";
                break;
            default:
                throw new \InvalidArgumentException("Unknown command: " . $task->getCommand());
        }
    }
}