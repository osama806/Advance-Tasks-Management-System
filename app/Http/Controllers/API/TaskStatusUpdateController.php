<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeTaskStatus\ChangeTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskStatusUpdateService;
use App\Traits\ResponseTrait;

class TaskStatusUpdateController extends Controller
{
    use ResponseTrait;
    protected $taskStatusUpdateService;

    public function __construct(TaskStatusUpdateService $taskStatusUpdateService)
    {
        $this->taskStatusUpdateService = $taskStatusUpdateService;
    }

    /**
     * Change status task
     * @param \App\Http\Requests\ChangeTaskStatus\ChangeTaskStatusRequest $changeTaskStatusRequest
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function changeStatus(ChangeTaskStatusRequest $changeTaskStatusRequest, $id)
    {
        $task = Task::findOrFail($id);
        if (!$task) {
            return $this->getResponse('error', 'Task Not Found', 404);
        }
        $validedData = $changeTaskStatusRequest->validated();
        $response = $this->taskStatusUpdateService->changeStatus($validedData, $task);
        return $response['status']
            ? $this->getResponse('msg', $response['msg'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }
}
