<?php

namespace App\Observer;

interface TaskObserverInterface
{
    public function update(Task $task): void;
}