<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminRoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
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
    public function store(Request $request)
    {
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
