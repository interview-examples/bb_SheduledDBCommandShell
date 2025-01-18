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
            $tasks = $this->repository->findAllTasks();
        } else {
            $tasks = $this->repository->findTasksPaginated($tasksPerPage, $offset);
        }
        $totalTasks = $this->repository->countAllTasks();

        if (PHP_SAPI === 'cli') {
            $this->renderCli($tasks, $page, $totalTasks, $tasksPerPage);
        } else {
            $this->renderWeb($tasks, $page, $totalTasks, $tasksPerPage);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function renderWeb($tasks, $page, $totalTasks, $tasksPerPage): void
    {
        if ($totalTasks > 0) {
            $this->twig->display('tasks/list.html.twig', [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks,
                'currentPage' => $page,
                'totalPages' => ceil($totalTasks / $tasksPerPage),
            ]);
        } else {
            $this->twig->display('tasks/empty_list.html.twig');
        }
    }

    private function renderCli($tasks, $page, $totalTasks, $tasksPerPage): void
    {
        if ($totalTasks > 0) {
            echo "Tasks (Page $page of " . ceil($totalTasks / $tasksPerPage) . "):\n\n";
            echo "+----+-----------------+-----------------+---------------------+----------+\n";
            echo "| ID | Command         | Description     | Execute At          | Status   |\n";
            echo "+----+-----------------+-----------------+---------------------+----------+\n";

            foreach ($tasks as $task) {
                echo sprintf(
                    "| %2d | %-15s | %-15s | %-19s | %-8s |\n",
                    $task->getId(),
                    $task->getCommand(),
                    $task->getDescription(),
                    $task->getExecuteAt(),
                    $this->cliColorStatus($task->getStatus())
                );
            }

            echo "+----+-----------------+-----------------+---------------------+----------+\n";
        } else {
            echo "No tasks available.\n";
        }
    }

    private function cliColorStatus(string $status): string
    {
        switch ($status) {
            case 'executed':
                return "\033[32m" . str_pad($status, 7) . "\033[0m";
            case 'error':
                return "\033[31m" . str_pad($status, 7) . "\033[0m";
            default:
                return str_pad($status, 7);
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
                'new'
            );
            $this->repository->addTask($task);
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
            $this->repository->editTimeTask($id, $task);
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->repository->deleteTask($id);
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function removeAll(): void
    {
        try {
            $this->repository->removeAllTasks();
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}