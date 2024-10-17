<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'comment-related-to'          =>      $this->commentable_type,
            'model-id'                    =>      $this->commentable_id,
            'comment-content'             =>      $this->content
        ];
    }
}
