<?php

require __DIR__ . '/../config/entrypoint.php';

$router->addRoute('GET', '/', function() use ($taskController) {
    $taskController->list();
});
$router->addRoute('GET', '/list', function() use ($taskController) {
    $page = $_GET['page'] ?? 1;
    $taskController->list((int)$page);
});
$router->addRoute('POST', '/create', function() use ($taskController) {
    $command = $_POST['command'];
    $description = $_POST['description'];
    $executeAt = $_POST['executeAt'];
    $taskController->create($command, $description, $executeAt);
});
$router->addRoute('POST', '/edit', function() use ($taskController) {
    $task_id = $_POST['id'];
    $new_executed = $_POST['executeAt'];
    $taskController->editTime((int)$task_id, $new_executed);
});
$router->addRoute('POST', '/delete', function() use ($taskController) {
    $task_id = $_POST['id'];
    $taskController->delete((int)$task_id);
});
$router->addRoute('GET', '/clear', function() use ($taskController) {
    $taskController->deleteAll();
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path);