<?php

namespace Feature;

use PHPUnit\Framework\TestCase;
use App\Controller\TaskController;
use App\Repository\TaskRepository;
use App\Model\Task;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

class TaskControllerTest extends TestCase
{
    private TaskRepository $repository;
    private Environment $twig;
    private TaskController $controller;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TaskRepository::class);

        $loader = new ArrayLoader([
            'tasks/list.html.twig' => 'List of tasks',
            'tasks/empty_list.html.twig' => 'No tasks available'
        ]);
        $this->twig = new Environment($loader);

        $this->controller = new TaskController($this->repository, $this->twig);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function testListTasksWeb()
    {
        $this->repository->expects($this->once())
            ->method('findAllTasks')
            ->willReturn([
                new Task('Write to DB', 'Test description', '2023-10-01 12:00:00', 'pending')
            ]);

        $this->repository->expects($this->once())
            ->method('countAllTasks')
            ->willReturn(1);

        ob_start();
        $this->controller->list();
        $output = ob_get_clean();

        $this->assertStringContainsString('Tasks (Page 0 of 1)', $output);
    }

    public function testListTasksCli(): void
    {
        $this->repository->expects($this->once())
            ->method('findAllTasks')
            ->willReturn([
                new Task('Write to DB', 'Test description', '2023-10-01 12:00:00', 'pending')
            ]);

        $this->repository->expects($this->once())
            ->method('countAllTasks')
            ->willReturn(1);

        $_SERVER['SAPI_NAME'] = 'cli';

        ob_start();
        $this->controller->list();
        $output = ob_get_clean();

        $this->assertStringContainsString('Tasks (Page 0 of 1)', $output);
    }
}