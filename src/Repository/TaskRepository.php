<?php

namespace App\Repository;

use App\Model\Task;
use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
        $row = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $row->execute(['id' => $id]);
        $task = $row->fetch();
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

    public function findTasksToExecute(string $currentTime): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE executeAt <= :currentTime AND status = 'pending'");
        $stmt->execute(['currentTime' => $currentTime]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
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

    public function clearTasks(): void
    {
        $this->pdo->query("DELETE FROM tasks");
    }

    public function findAllTasks(): array
    {
        return $this->pdo->query("SELECT * FROM tasks ORDER BY executeAt ASC")->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function findTasksPaginated(int $limit, float|int $offset): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($tasks as $task) {
            $objTask = new Task($task['command'], $task['description'], $task['executeAt'], $task['status']);
            $objTask->setId($task['id']);
            $result[] = $objTask;
        }

        return $result;
    }

    public function countAllTasks(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM tasks");

        return (int)$stmt->fetchColumn();
    }

    public function removeAllTasks()
    {
        $stmt = $this->pdo->query("DELETE FROM tasks");
    }
}