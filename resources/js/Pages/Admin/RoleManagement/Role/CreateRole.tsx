import React from "react";
import { Head, usePage } from "@inertiajs/react";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DottedSeparator } from "@/Components/DottedSeparator";
import { toast } from "sonner";
import { RoleNameInput } from "@/features/admin/roles/components/RoleNameInput";
import { SaveRoleButton } from "@/features/admin/roles/components/SaveRoleButton";
import { RolePermissionsTable } from "@/features/admin/roles/components/RolePermissionsTable";
import { useRoleManagement } from "@/features/admin/roles/hooks/useRoleManagement";
import { RolePermission } from "@/features/admin/roles/types/role";

interface CreateRoleProps {
    rolePermissions: RolePermission[];
}

export default function CreateRole({ rolePermissions }: CreateRoleProps) {
    const authUser = usePage().props.auth.user;
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
        errors
    } = useRoleManagement(rolePermissions);

    const handleSubmit = () => {
        post(route("admin.roles.store"), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                toast.success("Role created successfully");
            },
        });
    };

    return (
        <AdminAuthenticatedLayout>
            <Head title="Create Role" />
            <div className="h-full flex flex-col">
                <div className="flex-1 w-full border rounded-lg">
                    <div className="h-full flex flex-col overflow-auto p-4">
                        <div className="flex flex-col gap-y-2 lg:flex-row justify-between items-center">
                            <RoleNameInput
                                value={data.display_name}
                                onChange={(value) => setData("display_name", value)}
                                error={errors.display_name}
                            />
                            <SaveRoleButton
                                user={authUser}
                                onClick={handleSubmit}
                                processing={processing}
                            />
                        </div>
                        <DottedSeparator className="my-4" />
                        <RolePermissionsTable
                            rolePermissions={rolePermissions}
                            isChecked={isChecked}
                            onSelect={handleSelect}
                            onSelectAll={handleSelectAll}
                            hasOtherPermissions={hasOtherPermissions}
                        />
                    </div>
                </div>
            </div>
        </AdminAuthenticatedLayout>
    );
}
