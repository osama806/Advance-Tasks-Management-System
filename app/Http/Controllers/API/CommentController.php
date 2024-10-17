<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the comments.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Cache::remember('comments', 3600, function () {
            return Comment::all();
        });
        return $this->getResponse('comments', CommentResource::collection($comments), 200);
    }

    /**
     * Display the specified comment.
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return $this->getResponse('comments', new CommentResource($comment), 200);
    }

    /**
     * Remove the specified comment from storage.
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        if (!$comment) {
            return $this->getResponse('error', 'Comment Not Found', 404);
        }
        $comment->delete();
        return $this->getResponse('msg', 'Deleted Comment Successfully', 200);
    }
}
