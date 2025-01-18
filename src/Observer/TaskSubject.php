<?php

namespace App\Observer;

use App\Model\Task;
use App\Observer\TaskSubjectInterface;

class TaskSubject implements TaskSubjectInterface
{
    private array $observers = [];

    public function attach(TaskObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(TaskObserverInterface $observer): void
    {
        foreach ($this->observers as $key => $obs) {
            if ($obs === $observer) {
                unset($this->observers[$key]);
            }
        }
    }

    public function notify(Task $task): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($task);
        }
    }
}