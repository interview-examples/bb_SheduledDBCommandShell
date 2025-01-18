<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Utils\CommandOperands;
use App\Utils\InputSanitizer;
use InvalidArgumentException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\Task;

class TaskController
{
    private TaskRepository $repository;
    private Environment $twig;

    /**
     * TaskController constructor.
     *
     * @param TaskRepository $repository
     * @param Environment|null $twig
     */
    public function __construct(
        TaskRepository $repository,
        Environment|null $twig)
    {
        $this->repository = $repository;
        $this->twig = $twig;
    }

    /**
     * Preparing tasks to output by web or cli
     *
     * @param int $page
     *
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
     * Performs task actions (POST-request via web interface)
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function taskAction(): void
    {
        $action = $_POST['action'];
        switch ($action) {
            case 'create':
                $command = $_POST['command'] ?? '';
                $description = $_POST['description'] ?? '';
                $executeAt = $_POST['executeAt'] ?? '';
                $this->create($command, $description, $executeAt);
                break;
            case 'edit':
                $taskId = $_POST['taskId'] ?? '-1';
                $executeAt = $_POST['executeAt'] ?? '';
                $this->editTime((int)$taskId, $executeAt);
                break;
            case 'delete':
                $taskId = $_POST['taskId'] ?? '-1';
                $this->delete((int)$taskId);
                break;
            case 'deleteAll':
                $this->removeAll();
                break;
            default:
                throw new InvalidArgumentException("Unknown action: $action");
        }
        header('Location: /');
        exit;
    }

    /**
     * Output to web interface (using TWIG)
     *
     * @param array $tasks
     * @param int $page
     * @param int $totalTasks
     * @param int $tasksPerPage
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function renderWeb(
        array $tasks,
        int $page,
        int $totalTasks,
        int $tasksPerPage): void
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

    /**
     * Output to web interface (using TWIG)
     *
     * @param array $tasks
     * @param int $page
     * @param int $totalTasks
     * @param int $tasksPerPage
     */
    private function renderCli(
        array $tasks,
        int $page,
        int $totalTasks,
        int $tasksPerPage): void
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
        return match ($status) {
            'executed' => "\033[32m" . str_pad($status, 7) . "\033[0m",
            'error' => "\033[31m" . str_pad($status, 7) . "\033[0m",
            default => str_pad($status, 7),
        };
    }

    /**
     * Create a new task and add it to the database.
     *
     * @param string $command Command of the task. Possible values are "Write to DB", "Send email", "Out to screen" (case non-sensitive).
     * @param string $description Description of the task.
     * @param string $executeAt Date and time when the task should be executed. The format is "YYYY-MM-DD HH:MM:SS" or "+15m" or "HH:MM" etc.
     */
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

    /**
     * Edit the execute time of an existing task in the database.
     *
     * @param int $id The ID of the task to edit.
     * @param string $executeAt The new date and time when the task should be executed. The format is "YYYY-MM-DD HH:MM:SS" or "+15m" or "HH:MM" etc.
     *
     * @throws \InvalidArgumentException If the task with the given ID does not exist.
     */
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

    /**
     * Deletes a task from the database.
     *
     * @param int $id The ID of the task to delete.
     *
     * @throws \InvalidArgumentException If the task with the given ID does not exist.
     */
    public function delete(int $id): void
    {
        try {
            $this->repository->deleteTask($id);
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Removes all tasks from the database.
     *
     * If an error occurs during the deletion of tasks, an error message is output.
     */
    public function removeAll(): void
    {
        try {
            $this->repository->removeAllTasks();
        } catch (\InvalidArgumentException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}