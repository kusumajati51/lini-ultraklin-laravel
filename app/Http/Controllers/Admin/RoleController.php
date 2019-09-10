<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Role;

class RoleController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $roles = Role::paginate('24');

        return view('admin.role.index', compact(
            'roles'
        ));
    }

    public function showPermissions($name)
    {
        $role = Role::where('name', $name)->first();

        if (is_null($role)) return redirect('/admin/roles');

        $rolePermissions = $role->permissions->pluck('id');

        return view('admin.role.show', compact(
            'role', 'rolePermissions'
        ));
    }

    public function updatePermissions($name)
    {
        $role = Role::where('name', $name)->first();

        if (is_null($role)) return redirect('/admin/roles');

        $role->permissions()->detach();

        $role->permissions()->attach($this->request->permissions);

        return response()->json([
            'rolePermissions' => $role->permissions->pluck('id')
        ]);
    }
}
