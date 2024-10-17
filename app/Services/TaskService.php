<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Http\Services\assetsService;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskStatusUpdate;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskService
{
    use ResponseTrait;

    /**
     * Get list of tasks
     * @param array $data
     * @return array
     */
    public function index(array $data)
    {
        // Filter out null and empty string values
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        // If no filters are provided, return all tasks
        if (empty($filteredData)) {
            $tasks = Cache::remember('tasks', 3600, function () {
                return Task::all();
            });
        } else {
            $tasksQuery = Task::query();

            // Apply filters using local scopes or conditions
            $tasksQuery->priority($filteredData['priority'] ?? null);
            $tasksQuery->status($filteredData['status'] ?? null);
            $tasks = $tasksQuery->get();
        }

        return ['status' => true, 'tasks' => TaskResource::collection($tasks)];
    }

    /**
     * Create new task in storage
     * @param array $data
     * @return array
     */
    public function createTask(array $data)
    {
        $task = Task::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'priority'    => $data['priority'],
            'type'        => $data['type']
        ]);

        return $task
            ? ['status'    =>  true]
            : ['status'    =>  false, 'msg'    =>  'There is error in server', 'code'  =>  500];
    }

    /**
     * Update a spicified task details in storage
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function update(array $data, Task $task)
    {
        // return attributes value that not null and not empty
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return ['status' => false, 'msg' => 'Not Found Data in Request!', 'code' => 404];
        }

        $task->update($filteredData);

        return ['status'    =>  true];
    }

    /**
     * Remove a specified task from storage
     * @param \App\Models\Task $task
     * @return bool[]|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Task $task)
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role  && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }
        $task->delete();
        return ['status'    =>  true];
    }

    /**
     * Retrive a spicified task after deleted
     * @param \App\Models\Task $task
     * @return bool[]|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function restore(Task $task)
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role && $role->name !== 'admin') {
            return [
                'status'        =>      false,
                'msg'           =>      "Can't access delete permission",
                'code'          =>       400
            ];
        }

        // check if task deleted previous
        if ($task->deleted_at === null) {
            return [
                'status' => false,
                'msg' => "This task isn't deleted",
                'code' => 400,
            ];
        }

        // retrive task from delete
        $task->restore();

        return ['status' => true];
    }

    /**
     * Assigned a specified task to user
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function assign(array $data, Task $task)
    {
        // check if task assigned to user already previous
        if ($task->assigned_to !== null) {
            return ['status' => false, 'msg' => 'This task is already assigned to a user', 'code' => 400];
        }

        $user = User::find($data['assigned_to']);
        if (!$user) {
            return ['status' => false, 'msg' => 'User not found!', 'code' => 404];
        }

        // assign task to normal user (not allow assign to user as admin or manager role)
        $role = Role::where('user_id', $data['assigned_to'])->first();
        if ($role && $role->name !== 'user') {
            Log::info($role->name);
            return ['status' => false, 'msg' => "Can't assign task to this user", 'code' => 400];
        }

        try {
            // date with timezone
            $dueDate = Carbon::createFromFormat('d-m-Y H:i', $data['due_date']);

            // check if date is oldest not future
            if ($dueDate->isPast()) {
                return ['status' => false, 'msg' => 'Due date must be a future date.', 'code' => 400];
            }
        } catch (InvalidFormatException $e) {
            return ['status' => false, 'msg' => 'Invalid due date format, please use d-m-Y H:i', 'code' => 400];
        }

        $task->assigned_to = $data['assigned_to'];

        // date without timezone
        $task->due_date = $dueDate->toDateTime()->format('d-m-Y H:i');
        $task->status = 'Open';
        $task->save();

        TaskStatusUpdate::create([
            'task_id'           =>          $task->id,
            'status'            =>          $task->status
        ]);

        return ['status' => true];
    }

    /**
     * Create new comment to task
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function addComment(array $data, Task $task)
    {
        try {
            $task->comments()->create([
                'content'       =>      $data['content']
            ]);

            return [
                'status'        =>      true
            ];
        } catch (\Throwable $th) {
            return [
                'status'        =>      false,
                'msg'           =>      $th->getMessage(),
                'code'          =>      500
            ];
        }
    }

    /**
     * Create new attachment to task
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function addAttach(array $data, Task $task)
    {
        // Store the file using the assets service
        $assetsService = new assetsService();
        $fileURL = $assetsService->storeImage($data['file']);

        try {
            $task->attachments()->create([
                'file_path'       =>        $fileURL
            ]);

            return [
                'status'        =>      true
            ];
        } catch (\Throwable $th) {
            return [
                'status'        =>      false,
                'msg'           =>      $th->getMessage(),
                'code'          =>      500
            ];
        }
    }
}
