<?php

$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set($config['timezone']);

$configDB = require __DIR__ . '/../config/database.php';

use App\Framework\Router;
use App\Repository\DatabaseInitializer;
use App\Repository\TaskRepository;
use App\Controller\TaskController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

DatabaseInitializer::initialize($configDB);

$pdo = new PDO(
    $configDB['connection'].';dbname='.$configDB['dbname'],
    $configDB['username'],
    $configDB['password'],
    $configDB['options']
);

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader);

$taskRepository = new TaskRepository($pdo);
$taskController = new TaskController($taskRepository, $twig);

$router = new Router();
