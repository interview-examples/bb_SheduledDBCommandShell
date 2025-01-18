<?php

namespace App\Strategy;

use App\Model\Task;

interface TaskStrategyInterface
{
    public function execute(Task $task);
}