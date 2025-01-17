<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/database.php';

use App\Framework\Router;
use App\Repository\DatabaseInitializer;
use App\Repository\TaskRepository;
use App\Controller\TaskController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

DatabaseInitializer::initialize($config);

$pdo = new PDO(
    $config['connection'].';dbname='.$config['dbname'],
    $config['username'],
    $config['password'],
    $config['options']
);

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader);

$taskRepository = new TaskRepository($pdo);
$taskController = new TaskController($taskRepository, $twig);

$router = new Router();
