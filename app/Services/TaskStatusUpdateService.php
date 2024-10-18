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
    public function changeStatus(array $data, Task $task)
    {
        if ($data['status'] === 'In Progress') {
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
        }

        if ($data['status'] === 'Completed') {
            if ($task->status !== 'In Progress') {
                return ['status'    =>  false,  'msg' => 'Task Status Not In Progress', 'code'  =>   400];
            }

            // check if task assigned to auth user
            if ($task->assigned_to !== Auth::id()) {
                return ['status'    =>  false,  'msg' => 'This task assigned to another user', 'code'  =>   400];
            }

            $task->due_date = now()->toDateTime()->format('d-m-Y H:i');
        }

        $task->status = $data['status'];
        $task->save();

        TaskStatusUpdate::create([
            'task_id'       =>          $task->id,
            'status'        =>          $data['status'],
        ]);

        return [
            'status'        =>      true,
            'msg'           =>      $data['status'] === 'In Progress' ? 'Processing' : 'Completed'
        ];
    }
}
