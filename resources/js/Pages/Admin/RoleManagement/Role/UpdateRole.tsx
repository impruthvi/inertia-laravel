import { Head, usePage } from "@inertiajs/react";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DottedSeparator } from "@/Components/DottedSeparator";
import { toast } from "sonner";
import { RoleNameInput } from "@/features/admin/roles/components/RoleNameInput";
import { SaveRoleButton } from "@/features/admin/roles/components/SaveRoleButton";
import { RolePermissionsTable } from "@/features/admin/roles/components/RolePermissionsTable";
import { useRoleManagement } from "@/features/admin/roles/hooks/useRoleManagement";
import {
    Permission,
    Role,
    RolePermission,
} from "@/features/admin/roles/types/role";

interface UpdateRoleProps {
    rolePermissions: RolePermission[];
    selected_permissions: Record<number, string[]>;
    role: Role;
}

export default function UpdateRole({
    rolePermissions,
    role,
    selected_permissions,
}: UpdateRoleProps) {
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

    console.log(typedPermissions);
    

    const existingRole = {
        id: role.id,
        display_name: role.display_name,
        roles: typedPermissions,
    };

    const {
        data,
        setData,
        isChecked,
        handleSelect,
        handleSelectAll,
        processing,
        hasOtherPermissions,
        errors,
        put,
    } = useRoleManagement(rolePermissions, existingRole);

    const onSubmit = () => {
        put(route("admin.roles.update", role.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Role updated successfully");
            },
        });
    };

    return (
        <AdminAuthenticatedLayout>
            <Head title="Update Role" />
            <div className="h-full flex flex-col">
                <div className="flex-1 w-full border rounded-lg">
                    <div className="h-full flex flex-col overflow-auto p-4">
                        <div className="flex flex-col gap-y-2 lg:flex-row justify-between items-center">
                            <RoleNameInput
                                value={data.display_name}
                                onChange={(value) =>
                                    setData("display_name", value)
                                }
                                error={errors.display_name}
                            />
                            <SaveRoleButton
                                user={authUser}
                                onClick={onSubmit}
                                processing={processing}
                                isUpdate={true}
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
