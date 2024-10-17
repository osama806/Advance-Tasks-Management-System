<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'priority'    => $this->priority,
            'type'        => $this->type,
            'status'      => $this->status,
            'due_date'    => $this->due_date,
            'assigned_to' => $this->assigned_to
        ];
    }
}
