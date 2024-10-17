<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\FilterTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ResponseTrait;

    protected $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService  = $taskService;
    }

    /**
     * Display a listing of the tasks.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(FilterTaskRequest $filterFormRequest)
    {
        $validated = $filterFormRequest->validated();
        $response = $this->taskService->index($validated);
        return $response['status']
            ? $this->getResponse('tasks', $response['tasks'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get list of tasks assigned to auth user
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function myTasks()
    {
        $tasks = Task::where('assign_to', Auth::id())->get();
        return $this->getResponse('tasks', TaskResource::collection($tasks), 200);
    }

    /**
     * Store a newly created task in storage.
     * @param \App\Http\Requests\Task\StoreTaskRequest $storeFormRequest
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $storeFormRequest)
    {
        $validated = $storeFormRequest->validated();
        $response = $this->taskService->createTask($validated);
        return $response['status']
            ? $this->getResponse('msg', 'Created task is successfully', 201)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Display the specified task.
     * @param \App\Models\Task $task
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return $this->getResponse('task', new TaskResource($task), 200);
    }

    /**
     * Update the specified task in storage.
     * @param \App\Http\Requests\Task\UpdateTaskRequest $updateFormRequest
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $updateFormRequest, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->getResponse('error', 'Not Found This Task', 404);
        }
        $validated = $updateFormRequest->validated();
        $response = $this->taskService->update($validated, $task);
        return $response['status']
            ? $this->getResponse('msg', 'Updated Task Successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Remove the specified task from storage.
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->getResponse('error', 'Not Found This Task', 404);
        }
        $response = $this->taskService->delete($task);
        return $response['status']
            ? $this->getResponse('msg', 'Task deleted successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get list of tasks that soft deleted
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function showDeletedTasks()
    {
        if (Auth::user()->role === null) {
            return $this->getResponse('error', "You can't access to this permission", 400);
        }
        $tasks = Task::onlyTrashed()->get();
        return $this->getResponse('deleted-tasks', TaskResource::collection($tasks), 200);
    }

    /**
     * Retrive the specified task after deleted.
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function restore($id)
    {
        $task = Task::onlyTrashed()->find($id);
        if (!$task) {
            return $this->getResponse('error', 'Task not found or not soft-deleted', 404);
        }
        $response = $this->taskService->restore($task);
        return $response['status']
            ? $this->getResponse('msg', 'Task restored successfully', 200)
            : $this->getResponse('error', 'Failed to restore task', 500);
    }

    /**
     * Force delete Task from storage.
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function forceDeleteTask($id)
    {
        if (Auth::user()->role === null) {
            return $this->getResponse('error', "You can't access to this permission", 400);
        }
        $task = Task::find($id);
        if (!$task) {
            $task = Task::withTrashed()->find($id);
            if (!$task) {
                return $this->getResponse('error', 'Task Not Found', 404);
            }
        }
        $task->forceDelete();
        return $this->getResponse('msg', 'Deleted task permanently', 200);
    }

    /**
     * Assign task to specified user
     * @param \App\Http\Requests\Task\AssignTaskRequest $assignFormRequest
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function assign(AssignTaskRequest $assignFormRequest, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->getResponse('error', 'Not Found This Task', 404);
        }
        $validated = $assignFormRequest->validated();
        $response = $this->taskService->assign($validated, $task);
        return $response['status']
            ? $this->getResponse('msg', 'Assigned task successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Deliveried task to admin
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function taskDelivery($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->getResponse('error', 'Not Found This Task', 404);
        }
        $response = $this->taskService->delivery($task);
        return $response['status']
            ? $this->getResponse('msg', 'Task Deliveried Successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }
}
