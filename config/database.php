<?php
$config = [
    'dbname' => 'BeachBum',
    'username' => 'root',
    'password' => 'BeerSheba@16042024#',
    'connection' => 'mysql:host=localhost;port=3306',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
];

return $config;