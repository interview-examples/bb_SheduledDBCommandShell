<?php

namespace App\Observer;

use App\Model\Task;

interface TaskSubjectInterface {
    public function attach(TaskObserverInterface $observer): void;
    public function detach(TaskObserverInterface $observer): void;
    public function notify(Task $task): void;
}