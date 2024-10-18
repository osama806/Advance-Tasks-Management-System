<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AttachmentController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the attachments.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        $attachments = Cache::remember('attachments', 3600, function () {
            return Attachment::all();
        });

        return $this->getResponse('attachments', AttachmentResource::collection($attachments), 200);
    }

    /**
     * Display the specified attachment.
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Attachment $attachment)
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        return $this->getResponse('attachment', new AttachmentResource($attachment), 200);
    }

    /**
     * Remove the specified attachment from storage.
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        $attachment = Attachment::findOrFail($id);
        if (!$attachment) {
            return $this->getResponse('error', 'Attachment Not Found', 404);
        }
        $attachment->delete();
        return $this->getResponse('msg', 'Deleted Attachment Successfully', 200);
    }
}
