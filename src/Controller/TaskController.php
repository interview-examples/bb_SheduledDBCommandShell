<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Service\TaskDataService;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TaskController
{
    private TaskRepository $repository;
    private TaskDataService $service;
    private Environment $twig;

    public function __construct(
        TaskRepository $repository,
        TaskDataService $service,
        Environment|null $twig)
    {
        $this->repository = $repository;
        $this->service = $service;
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
        // ToDo
    }

    public function editTime(mixed $id, mixed $executeAt): void
    {
        // ToDo
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