<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskStatusUpdate;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class TaskStatusUpdateService
{
    use ResponseTrait;

    /**
     * Change task status by only user
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function processing(array $data, Task $task)
    {
        if ($task->status !== 'Open') {
            return [
                'status'        =>      false,
                'msg'           =>      "This Task Not Open Status!",
                'code'          =>      400
            ];
        }
        if ($task->assigned_to !== Auth::id()) {
            return [
                'status'        =>      false,
                'msg'           =>      "You haven't this task",
                'code'          =>      403
            ];
        }

        $task->status = $data['status'];
        $task->save();

        TaskStatusUpdate::create([
            'task_id'       =>          $task->id,
            'status'        =>          $data['status'],
        ]);

        return [
            'status'        =>      true
        ];
    }

    /**
     * Deliveried a specified task to admin in specific time
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function delivery(array $data, Task $task)
    {
        if ($task->status !== 'In Progress') {
            return ['status'    =>  false,  'msg' => 'Task Status Not In Progress', 'code'  =>   400];
        }

        // check if task assigned to auth user
        if ($task->assigned_to !== Auth::id()) {
            return ['status'    =>  false,  'msg' => 'This task assigned to another user', 'code'  =>   400];
        }

        $task->status = $data['status'];
        $task->due_date = now()->toDateTime()->format('d-m-Y H:i');
        $task->save();

        TaskStatusUpdate::create([
            'task_id'       =>      $task->id,
            'status'        =>      $data['status']
        ]);
        return ['status'    =>  true];
    }
}
