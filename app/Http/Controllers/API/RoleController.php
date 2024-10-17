<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the roles.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }
        $roles = Role::with('users')->get();
        return $this->getResponse('roles', RoleResource::collection($roles), 200);
    }

    /**
     * Display the specified role.
     * @param \App\Models\Role $role
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        if (Auth::user()->role->name !== 'admin') {
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
        if (Auth::user()->role->name !== 'admin') {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }
        $role->delete();
        return $this->getResponse('msg', "Deleted Role Successfully", 200);
    }
}
