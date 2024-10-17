<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Auth;
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
            $tasks = Task::all();
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
            'created_by'  => Auth::user()->role->name
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
        // manager can control in tasks that he created only
        if (Auth::user()->role == 'manager' && $task->created_by !== 'manager') {
            return [
                'status'        =>      false,
                'msg'           =>      'This task not create from you!',
                'code'          =>      400
            ];
        }
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
        if (!Auth::check() || Auth::user()->role !== "admin") {
            return ['status'    =>  false,  'msg' => "Can't access delete permission", 'code' => 400];
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
        if (Auth::user()->role !== "admin") {
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
        if ($task->assign_to !== null) {
            return ['status' => false, 'msg' => 'This task is already assigned to a user', 'code' => 400];
        }

        $user = User::find($data['assign_to']);
        if (!$user) {
            return ['status' => false, 'msg' => 'User not found!', 'code' => 404];
        }

        // assign task to normal user (not allow assign to user as admin or manager role)
        if ($user->role !== null) {
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

        $task->assign_to = $data['assign_to'];

        // date without timezone
        $task->due_date = $dueDate->toDateTime()->format('d-m-Y H:i');
        $task->status = 'in-progress';
        $task->save();

        return ['status' => true];
    }

    /**
     * Deliveried a specified task to admin in specific time
     * @param \App\Models\Task $task
     * @return array
     */
    public function delivery(Task $task)
    {
        if (Auth::user()->role !== null || $task->status !== 'in-progress') {
            Log::info($task->status);
            Log::info(Auth::user()->role);
            return ['status'    =>  false,  'msg' => 'User unAuthorization or task status not in-progress', 'code'  =>   400];
        }

        // check if task assigned to auth user
        if ($task->assign_to !== Auth::id()) {
            return ['status'    =>  false,  'msg' => 'This task assigned to another user', 'code'  =>   400];
        }

        $task->status = 'done';
        $task->due_date = now()->toDateTime()->format('d-m-Y H:i');
        $task->save();
        return ['status'    =>  true];
    }
}
