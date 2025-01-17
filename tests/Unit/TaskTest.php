<?php

namespace Unit;

use App\Model\Task;
use App\Service\TaskDataService;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCreation()
    {
        $taskDataService = $this->createMock(TaskDataService::class);

        $description = 'Test task';
        $executeAt = '2023-01-01 12:00:00';

        $taskDataService->method('validateTime')->willReturn($executeAt);
        $taskDataService->method('convertStringToTime')->willReturn($executeAt);
        $taskDataService->method('convertTimeToString')->willReturn($executeAt);

        $task = new Task($description, $executeAt, $taskDataService);

        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals($executeAt, $task->getExecuteAt());
    }
}