<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\RoleInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(
        protected RoleInterface $roleInterface
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize(get_ability('add'));

        $roles = $this->roleInterface->get(
            select: ['id', 'display_name'],
            paginate: false
        );

        $role = $request->filled('role')
            ? $this->roleInterface->find($request->role)
            : [];

        $selectedPermissions = ($role)
            ? permission_to_array($role->permissions->pluck('name')->toArray(), Auth::user()->role)
            : [];

        return inertia('Admin/RoleManagement/Admin/Create', [
            'roles' => $roles,
            'rolePermissions' => role_permissions(Auth::user()->role),
            'role' => $role,
            'selected_permissions' => $selectedPermissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize(get_ability('add'));
        dd($request->all());
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
