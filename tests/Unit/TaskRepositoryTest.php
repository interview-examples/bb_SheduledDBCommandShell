<?php

namespace Unit;

use PHPUnit\Framework\TestCase;
use App\Repository\TaskRepository;
use App\Model\Task;
use PDO;
use PDOStatement;

class TaskRepositoryTest extends TestCase
{
    private PDO $pdo;
    private TaskRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new TaskRepository($this->pdo, 'Asia/Jerusalem');
    }

    public function testCreateTask()
    {
        $task = new Task('Write to DB', 'Test description', '2023-10-01 12:00:00', 'pending');

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with($this->arrayHasKey('command'));

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $this->repository->addTask($task);
    }

    public function testFindTask(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 1,
                'command' => 'Write to DB',
                'description' => 'Test description',
                'executeAt' => '2023-10-01 12:00:00',
                'status' => 'pending'
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $task = $this->repository->findTaskById(1);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals("write to db", $task->getCommand());
    }
}