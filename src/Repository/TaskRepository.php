<?php

namespace App\Repository;

use App\Model\Task;
use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(Task $task): Task {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (command, description, executeAt) VALUES (:command, :description, :executeAt)");
        $stmt->execute([
            'command' => $task->getCommand(),
            'description' => $task->getDescription(),
            'executeAt' => $task->getExecuteAt(),
        ]);
        $task->setId($this->pdo->lastInsertId());
        return $task;
    }

    public function findAll(): array
    {
        return $this->pdo->query("SELECT * FROM tasks ORDER BY executeAt ASC")->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function findPaginated(int $limit, float|int $offset): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM tasks");
        return (int)$stmt->fetchColumn();
    }
}