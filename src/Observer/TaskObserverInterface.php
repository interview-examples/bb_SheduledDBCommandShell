<?php

namespace App\Observer;

use App\Model\Task;

interface TaskObserverInterface
{
    public function update(Task $task): void;
}