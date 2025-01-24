<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\AdminRequest;
use App\Interfaces\AdminInterface;
use App\Interfaces\RoleInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class AdminController extends Controller
{
    public function __construct(
        private readonly RoleInterface $roleInterface,
        private readonly AdminInterface $adminInterface
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
            select: ['id', 'first_name', 'last_name', 'email', 'role'],
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
        $user = auth('admin')->user();

        /**
         * @var \App\Models\Role | null $role
         */
        $role = $request->filled('role') && is_string($request->role)
            ? $this->roleInterface->find($request->role)
            : null;

        $selectedPermissions = ($role)
            // @phpstan-ignore-next-line
            ? permission_to_array($role->permissions->pluck('name')->toArray(), auth('admin')->user()->role)
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
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id): Response|RedirectResponse
    {
        $this->authorize(get_ability('edit'));

        $roles = $this->roleInterface->get(
            select: ['id', 'display_name'],
            paginate: false
        );

        $admin = $this->adminInterface->find($id);

        if (! $admin instanceof \App\Models\Admin) {
            return redirect()->back()->with('error', trans('messages.not_found', ['entity' => 'Admin']));
        }

        /**
         * @var \App\Models\Admin $user
         */
        $user = auth('admin')->user();

        // Ensure $role is not null before using it
        $role = $request->filled('role') && is_string($request->role)
            ? $this->roleInterface->find($request->role)
            : $admin;
        // @phpstan-ignore-next-line
        $selectedPermissions = permission_to_array($role->permissions->pluck('name')->toArray(), auth('admin')->user()->role);

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
         * @var \App\Models\Admin|null $user
         */
        $user = $this->adminInterface->find($id);

        if (! $user instanceof \App\Models\Admin) {
            return redirect()->back()->with('error', trans('messages.not_found', ['entity' => 'Admin']));
        }

        // Ensure the condition is only true when comparing the correct properties
        if (
            $user->id === auth('admin')->id() &&
            // @phpstan-ignore-next-line
            ($user->role_id !== $request->role_id || ($user->custom_permission ?? null) !== $request->validated()['custom_permission'])
        ) {
            return redirect()->back()->with('error', trans('messages.cant_change_own'));
        }

        // Update the user
        $this->adminInterface->update($user, $request->validated());

        return redirect()->back()->with('success', trans('messages.updated', ['entity' => 'Admin']));
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
