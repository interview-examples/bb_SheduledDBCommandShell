<?php

namespace Unit;

use App\Model\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCreation()
    {
        $command = 'Test command';
        $description = 'Test task';
        $executeAt = '2023-01-01 12:00:00';
        $status = 0;

/*        $taskDataService->method('validateTime')->willReturn($executeAt);
        $taskDataService->method('convertStringToTime')->willReturn($executeAt);
        $taskDataService->method('convertTimeToString')->willReturn($executeAt);*/

        $task = new Task($command, $description, $executeAt, $status);

        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals($executeAt, $task->getExecuteAt());
    }
}