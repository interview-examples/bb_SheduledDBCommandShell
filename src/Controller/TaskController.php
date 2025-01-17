<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Utils\CommandOperands;
use App\Utils\InputSanitizer;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\Task;

class TaskController
{
    private TaskRepository $repository;
    private Environment $twig;

    public function __construct(
        TaskRepository $repository,
        Environment|null $twig)
    {
        $this->repository = $repository;
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function list(int $page = 0): void
    {
        $tasksPerPage = 20;
        $offset = ($page - 1) * $tasksPerPage;

        if ($offset < 0) {
            $tasks = $this->repository->findAll();
        } else {
            $tasks = $this->repository->findPaginated($tasksPerPage, $offset);
        }
        $totalTasks = $this->repository->countAll();

        if ($totalTasks > 0) {
            $this->twig->display('tasks/list.html.twig', [
                'tasks' => $tasks,
                'currentPage' => $page,
                'totalPages' => ceil($totalTasks / $tasksPerPage),
            ]);
        } else {
            $this->twig->display('tasks/empty_list.html.twig');
        }
    }

    public function create(String $command,
                           String $description,
                           String $executeAt): void
    {
        try {
            $command = CommandOperands::validateCommand($command);
            $task = new Task(
                $command,
                InputSanitizer::cleanString($description),
                $executeAt,
                'new',
                $this->service
            );
            $this->repository->create($task);
            $task->setStatus('created');
            echo "Task created successfully.\n";
        } catch (InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function editTime(int $id, string $executeAt): void
    {
        try {
            $task = $this->repository->findTaskById($id);
            $task->setExecuteAt($executeAt);
            $this->repository->editTime($id, $task);
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function delete(mixed $id): void
    {
        // ToDo
    }

    public function deleteAll(): void
    {
        // ToDo
    }
}