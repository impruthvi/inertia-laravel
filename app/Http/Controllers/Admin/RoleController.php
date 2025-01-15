<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\RoleRequest;
use App\Interfaces\RoleInterface;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoleController extends Controller
{

    function __construct(protected RoleInterface $roleInterface) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd("Role Index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(get_ability('add'));

        return Inertia::render('Admin/RoleManagement/Role/CreateRole', [
            'rolePermissions' => role_permissions(AdminRoleEnum::ADMIN->value),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $this->authorize(get_ability('add'));

        $this->roleInterface->store($request->all());

        return redirect()->back()->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize(get_ability('edit'));

        $role = $this->roleInterface->find($id);

        if (empty($role)) {
            return to_route('admin.roles.index')->with([
                'error' => __('messages.not_found', ['entity' => 'Role']),
                'uid' => Str::uuid(),
            ]);
        }

        $selectedPermissions = permission_to_array($role->permissions->pluck('name')->toArray(), AdminRoleEnum::ADMIN->value);

        return inertia('Admin/RoleManagement/Role/UpdateRole', [
            'rolePermissions' => role_permissions(AdminRoleEnum::ADMIN->value),
            'selected_permissions' => $selectedPermissions,
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        $this->authorize(get_ability('edit'));

        $role = $this->roleInterface->find($id);

        $this->roleInterface->update($role->id, $request->all());

        return redirect()->back()->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
