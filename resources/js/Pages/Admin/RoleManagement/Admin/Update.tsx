import { useEffect } from "react";
import { Head, useForm, usePage } from "@inertiajs/react";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DottedSeparator } from "@/Components/DottedSeparator";
import { SaveRoleButton } from "@/features/admin/roles/components/SaveRoleButton";
import { RolePermissionsTable } from "@/features/admin/roles/components/RolePermissionsTable";
import {
    Permission,
    Permission as PermissionType,
    Role,
    RolePermission,
} from "@/features/admin/roles/types/role";
import { Card } from "@/Components/ui/card";
import { isEqual } from "lodash";
import { Admin } from "@/features/admin/admins/types/admin";
import { AdminEditForm } from "@/features/admin/admins/components/edit-admin-form";

interface UpdateAdminProps {
    rolePermissions: RolePermission[];
    roles: Role[];
    role: Role;
    selected_permissions: Record<number, string[]>;
    admin: Admin;
}

type FormData = {
    id: number | null;
    first_name: string | undefined;
    last_name: string | undefined;
    email: string | undefined;
    role_id: number | string | null;
    custom_permission: Record<number, Array<string>>;
};

export default function UpdateAdmin({
    rolePermissions,
    roles,
    role,
    selected_permissions,
    admin,
}: UpdateAdminProps) {
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

    const { data, setData, put, errors, reset, processing } =
        useForm<FormData>({
            id: admin.id ?? null,
            first_name: admin.first_name ?? "",
            last_name: admin.last_name ?? "",
            email: admin.email ?? "",
            role_id: admin.role_id ?? null,
            custom_permission: selected_permissions ?? {},
        });

    useEffect(() => {
        if (!isEqual(selected_permissions, data.custom_permission)) {
            setData("custom_permission", typedPermissions);
        }
    }, [selected_permissions]);

    const isChecked = (id: number, type: PermissionType | "all") => {
        if (type === "all") {
            const roles = data.custom_permission[id];
            return roles?.length === rolePermissions[id - 1].permissions.length;
        }

        if (data.custom_permission && data?.custom_permission[id]) {
            return data.custom_permission[id].includes(type);
        }

        return false;
    };

    const handleSubmit = () => {
        put(route("admin.admins.update", admin.id), {
            preserveScroll: true,
        });
    };

    const handleSelect = (e: any) => {
        const roles = data.custom_permission;
        const { value, dataset, checked } = e.target;

        if (checked) {
            // Adding elements to object
            if (!roles.hasOwnProperty(dataset.id)) {
                // adding new key into object
                roles[dataset.id] =
                    value !== "view" ? [value, "view"] : [value];
            } else {
                // updating already existed key
                roles[dataset.id] = [...roles[dataset.id], value];
            }
        } else {
            // Removing elements from object
            roles[dataset.id] = roles[dataset.id].filter(
                (item: any) => item !== value
            );

            if (roles[dataset.id].length === 0) delete roles[dataset.id];
        }

        setData("custom_permission", roles);
    };

    const handleSelectAll = (e: any) => {
        const roles = data.custom_permission;
        const { dataset, checked } = e.target;

        if (checked) {
            roles[dataset.id] = rolePermissions[dataset.id - 1]?.permissions;
        } else {
            delete roles[dataset.id];
        }

        setData("custom_permission", roles);
    };

    const hasOtherPermissions = (id: number): boolean => {
        const roles = data.custom_permission[id];
        if (!roles) return false;

        return roles.some((permission) => permission !== "view");
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
                            isUpdate
                        />
                    </div>

                    <DottedSeparator />

                    <AdminEditForm
                        data={data}
                        setData={setData}
                        errors={errors}
                        roles={roles}
                        role={role}
                        admin={admin}
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
