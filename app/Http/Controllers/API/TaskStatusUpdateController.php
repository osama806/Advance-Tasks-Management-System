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
     * Change task status to In Progress
     * @param \App\Http\Requests\ChangeTaskStatus\ChangeTaskStatusRequest $changeTaskStatusRequest
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function taskProcessing(ChangeTaskStatusRequest $changeTaskStatusRequest, $id)
    {
        $task = Task::findOrFail($id);
        if (!$task) {
            return $this->getResponse('error', 'Task Not Found', 404);
        }
        $validedData = $changeTaskStatusRequest->validated();
        $response = $this->taskStatusUpdateService->processing($validedData, $task);
        return $response['status']
            ? $this->getResponse('msg', 'Task Status is Processing', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Deliveried task to admin
     * @param \App\Http\Requests\ChangeTaskStatus\ChangeTaskStatusRequest $changeTaskStatusRequest
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function taskDelivery(ChangeTaskStatusRequest $changeTaskStatusRequest, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->getResponse('error', 'Not Found This Task', 404);
        }
        $validatedData = $changeTaskStatusRequest->validated();
        $response = $this->taskStatusUpdateService->delivery($validatedData, $task);
        return $response['status']
            ? $this->getResponse('msg', 'Task Status is Completed', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }
}
