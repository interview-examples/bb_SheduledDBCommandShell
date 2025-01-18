<?php

namespace App\Repository;

use App\Model\Task;
use Carbon\Carbon;
use PDO;

class TaskRepository
{
    private PDO $pdo;
    private string $timezone;

    public function __construct(PDO $pdo, string $timezone = 'Asia/Jerusalem')
    {
        $this->pdo = $pdo;
        $this->timezone = $timezone;
    }

    public function addTask(Task $task): Task
    {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (command, description, executeAt, status) 
VALUES (:command, :description, :executeAt, :status)");
        $stmt->execute([
            'command' => $task->getCommand(),
            'description' => $task->getDescription(),
            'executeAt' => $task->getExecuteAt(),
            'status' =>'pending'
        ]);
        $task->setId($this->pdo->lastInsertId());
        return $task;
    }

    public function findTaskById(int $id): Task
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();
        return new Task($task['command'], $task['description'], $task['executeAt'], $task['status']);
    }

    public function editTimeTask(int $id, Task $task): Task
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET executeAt = :executeAt, status = :status WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'executeAt' => $task->getExecuteAt(),
            'status' =>'pending'
        ]);
        return $task;
    }

    /**
     * Find tasks that should be executed between $startTime and $endTime.
     *
     * @param string $startTime The start time (inclusive) of the time range.
     * @param string $endTime The end time (inclusive) of the time range.
     * @return Task[] The tasks that should be executed between $startTime and $endTime.
     */
    public function findTasksToExecute(string $startTime, string $endTime): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE (executeAt BETWEEN :startTime AND :endTime) AND status = 'pending'");
        $stmt->execute(['startTime' => $startTime, 'endTime' => $endTime]);

        return $this->fetchResultToTasksObject(
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function updateTaskStatus(int $taskId, string $status): void
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $taskId]);
    }

    public function deleteTask(int $taskId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);
    }

    public function findAllTasks(): array
    {
        return $this->fetchResultToTasksObject(
            $this->pdo->query("SELECT * FROM tasks ORDER BY executeAt ASC")->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findTasksPaginated(int $limit, float|int $offset): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->fetchResultToTasksObject($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countAllTasks(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM tasks");

        return (int)$stmt->fetchColumn();
    }

    public function removeAllTasks(): void
    {
        $this->pdo->query("DELETE FROM tasks");
    }

    private function fetchResultToTasksObject(array $queryResult): array {
        $tasks = [];
        foreach ($queryResult as $task) {
            $objTask = new Task($task['command'], $task['description'], $task['executeAt'], $task['status']);
            $objTask->setId($task['id']);
            $tasks[] = $objTask;
        }
        return $tasks;
    }

    /**
     * Emulates "write to DB" command. Stores task with ID $id and description $description to the 'log_executed_tasks' table.
     *
     * @param int $id The ID of the task that was executed.
     * @param string $description A description of the executed task.
     */
    public function executeWriteToDBTask(int $id, string $description): void
    {
        global $config;
        $dateTime = Carbon::now();
        $dateTime->setTimezone($this->timezone);
        $stmt = $this->pdo->prepare("INSERT INTO log_executed_tasks (taskId, description, executedAt) 
VALUES (:id, :description, :executedAt)");
        $stmt->execute([
            'id' => $id,
            'description' => $description,
            'executeAt' => $dateTime
        ]);
    }
}