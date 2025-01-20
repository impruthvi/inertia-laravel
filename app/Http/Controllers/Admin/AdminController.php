<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\AdminRequest;
use App\Interfaces\AdminInterface;
use App\Interfaces\RoleInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function __construct(
        protected RoleInterface $roleInterface,
        protected AdminInterface $adminInterface
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize(get_ability('view'));
        // Extract and sanitize input
        $search = $request->input('search');
        $sortArray = $request->input('sort', []);

        // Apply search filter
        $filters = [
            'search' => $search,
            'sort' => $sortArray,
        ];


        $admins = $this->adminInterface->get(
            select: ['id', 'name', 'email', 'role'],
            filters: $filters,
            paginate: true
        );

        return Inertia::render('Admin/RoleManagement/Admin/Index', [
            'admins' => $admins,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $this->authorize(get_ability('add'));

        $roles = $this->roleInterface->get(
            select: ['id', 'display_name'],
            paginate: false
        );

        /**
         * @var \App\Models\Admin $user
         */
        $user = Auth::user();

        /**
         * @var \App\Models\Role | null $role
         */
        $role = $request->filled('role') && is_string($request->role)
            ? $this->roleInterface->find($request->role)
            : null;

        $selectedPermissions = ($role)
            // @phpstan-ignore-next-line
            ? permission_to_array($role->permissions->pluck('name')->toArray(), Auth::user()->role)
            : [];

        return Inertia::render('Admin/RoleManagement/Admin/Create', [
            'roles' => $roles,
            'rolePermissions' => role_permissions($user->role),
            'role' => $role,
            'selected_permissions' => $selectedPermissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request): RedirectResponse
    {
        $this->authorize(get_ability('add'));

        $this->adminInterface->store($request->validated());

        return redirect()->back()->with('success', 'Admin created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id): Response
    {
        $this->authorize(get_ability('edit'));

        $roles = $this->roleInterface->get(
            select: ['id', 'display_name'],
            paginate: false
        );

        $admin = $this->adminInterface->find($id);

        /**
         * @var \App\Models\Admin $user
         */
        $user = Auth::user();


        // Ensure $role is not null before using it
        $role = $request->filled('role') && is_string($request->role)
            ? $this->roleInterface->find($request->role)
            : $admin;
        // @phpstan-ignore-next-line
        $selectedPermissions = permission_to_array($role->permissions->pluck('name')->toArray(), Auth::user()->role);

        return Inertia::render('Admin/RoleManagement/Admin/Update', [
            'roles' => $roles,
            'rolePermissions' => role_permissions($user->role),
            'admin' => $admin,
            'selected_permissions' => $selectedPermissions,
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, string $id): RedirectResponse
    {
        $this->authorize(get_ability('edit'));

        /**
         * @var \App\Models\Admin $user
         */
        $user = $this->adminInterface->find($id);

        // Ensure the condition is only true when comparing the correct properties
        if (
            $user->id == Auth::id() &&
            ($user->role_id != $request->role_id || ($user->custom_permission ?? null) != $request->validated()['custom_permission'])
        ) {
            return redirect()->back()->with('error', 'You cannot change your own role or permissions');
        }

        // Update the user
        $this->adminInterface->update((string) $user->id, $request->validated());

        return redirect()->back()->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize(get_ability('delete'));

        $this->adminInterface->delete($id);

        return redirect()->back()->with('success', 'Admin deleted successfully');
    }
}
