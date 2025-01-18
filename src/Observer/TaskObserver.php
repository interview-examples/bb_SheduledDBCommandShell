<?php

namespace App\Observer;

use App\Repository\TaskRepository;
use App\Strategy\TaskStrategy;
use App\Model\Task;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use RuntimeException;

class TaskObserver implements TaskObserverInterface
{
    private TaskRepository $taskRepository;
    private TaskStrategy $taskStrategy;

    public function __construct(TaskRepository $taskRepository, TaskStrategy $executionStrategy) {
        $this->taskRepository = $taskRepository;
        $this->taskStrategy = $executionStrategy;
    }

    public function update(Task $task): void
    {
        try {
            $this->taskStrategy->execute($task);
            $this->taskRepository->updateTaskStatus($task->getId(), 'executed');
        } catch (RuntimeException $e) {
            $this->taskRepository->updateTaskStatus($task->getId(), 'error');
            echo "Error executing task ID " . $task->getId() . ": " . $e->getMessage() . "\n";
        }
    }
}