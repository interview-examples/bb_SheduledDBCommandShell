<?php

namespace App\Repository;

use PDO;
use PDOException;

class DatabaseInitializer
{
    public static function initialize($config): void {
        try {
            $pdo = new PDO($config['connection'], $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dbname = "`".str_replace("`","``",$config['dbname'])."`";
            $pdo->query("CREATE DATABASE IF NOT EXISTS $dbname");
            //echo "Database '{$config['dbname']}' has been created successfully (or existed before).\n";

            $pdo->query("USE {$dbname}");
            self::createTableTask($pdo);
        } catch (PDOException $e) {
            die("Problem to connect to DB Server {$config['connection']}: " . $e->getMessage());
        }
    }

    private static function createTableTask(PDO $pdo): void {
        try {
            $pdo->query("CREATE TABLE IF NOT EXISTS tasks (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                command VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                executeAt DATETIME NOT NULL
            )");
            //echo "Table 'tasks' has been created successfully (or existed before).\n";
        } catch(PDOException $e) {
            die("Problem to create table 'tasks': " . $e->getMessage());
        }
    }
}