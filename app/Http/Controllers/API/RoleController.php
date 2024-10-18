<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the roles.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $checkAuth = Role::where('user_id', Auth::id())->first();
        if ($checkAuth && $checkAuth->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        $roles = Cache::remember('roles', 3600, function () {
            return Role::all();
        });

        return $this->getResponse('roles', RoleResource::collection($roles), 200);
    }

    /**
     * Display the specified role.
     * @param \App\Models\Role $role
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $checkAuth = Role::where('user_id', Auth::id())->first();
        if ($checkAuth && $checkAuth->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        try {
            return $this->getResponse('role', new RoleResource($role), 200);
        } catch (ModelNotFoundException $e) {
            return $this->getResponse('error', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified role from storage.
     * @param \App\Models\Role $role
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $checkAuth = Role::where('user_id', Auth::id())->first();
        if ($checkAuth && $checkAuth->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 422);
        }
        $role->delete();
        return $this->getResponse('msg', "Deleted Role Successfully", 200);
    }
}
