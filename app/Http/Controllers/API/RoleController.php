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

class RoleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the roles.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::where('user_id', Auth::id())->first();
        if ($role &&$role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }
        $roles = Cache::remember('roles', 3600, function () {
            return Role::with('users')->get();
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
        $role = Role::where('user_id', Auth::id())->first();
        if ($role && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
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
        $role = Role::where('user_id', Auth::id())->first();
        if ($role  && $role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }
        $role->delete();
        return $this->getResponse('msg', "Deleted Role Successfully", 200);
    }
}
