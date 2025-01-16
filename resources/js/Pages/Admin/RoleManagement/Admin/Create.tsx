import React, { useEffect, useState } from "react";
import { Head, usePage } from "@inertiajs/react";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DottedSeparator } from "@/Components/DottedSeparator";
import { toast } from "sonner";
import { SaveRoleButton } from "@/features/admin/roles/components/SaveRoleButton";
import { RolePermissionsTable } from "@/features/admin/roles/components/RolePermissionsTable";
import { useRoleManagement } from "@/features/admin/roles/hooks/useRoleManagement";
import {
    Permission,
    Role,
    RolePermission,
} from "@/features/admin/roles/types/role";
import { Card } from "@/Components/ui/card";
import { AdminCreateForm } from "@/features/admin/admins/components/create-admin-form";

interface CreateAdminProps {
    rolePermissions: RolePermission[];
    roles: Role[];
    role?: Role;
    selected_permissions: Record<number, string[]>;
}

export default function CreateAdmin({
    rolePermissions,
    roles,
    role,
    selected_permissions,
}: CreateAdminProps) {
    const authUser = usePage().props.auth.user;

    // Convert the permissions to the correct type
    const typedPermissions: Record<number, Permission[]> = Object.entries(
        selected_permissions
    ).reduce(
        (acc, [key, values]) => ({
            ...acc,
            [key]: values.filter((value): value is Permission =>
                ["view", "add", "edit", "delete"].includes(value)
            ),
        }),
        {}
    );

    const {
        data,
        setData,
        isChecked,
        handleSelect,
        handleSelectAll,
        post,
        reset,
        processing,
        hasOtherPermissions,
        errors,
    } = useRoleManagement(rolePermissions, undefined, typedPermissions);

    const handleSubmit = () => {
        post(route("admin.admins.store"), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                toast.success("Admin created successfully");
            },
        });
    };

    return (
        <AdminAuthenticatedLayout>
            <Head title="Create Admin" />
            <Card className="p-6">
                <div className="flex flex-col space-y-6">
                    <div className="flex justify-between items-center">
                        <h2 className="text-2xl font-bold text-gray-900">
                            Create Admin
                        </h2>
                        <SaveRoleButton
                            user={authUser}
                            onClick={handleSubmit}
                            processing={processing}
                        />
                    </div>

                    <DottedSeparator />

                    <AdminCreateForm
                        data={data}
                        setData={setData}
                        errors={errors}
                        roles={roles}
                    />

                    <DottedSeparator />

                    <RolePermissionsTable
                        rolePermissions={rolePermissions}
                        isChecked={isChecked}
                        onSelect={handleSelect}
                        onSelectAll={handleSelectAll}
                        hasOtherPermissions={hasOtherPermissions}
                    />
                </div>
            </Card>
        </AdminAuthenticatedLayout>
    );
}
