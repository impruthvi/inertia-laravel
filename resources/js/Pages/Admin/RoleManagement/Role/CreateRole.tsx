import React from "react";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
import InputError from "@/Components/InputError";
import Checkbox from "@/Components/Checkbox";
import { Head, useForm, usePage } from "@inertiajs/react";
import TextInput from "@/Components/TextInput";
import { HasAbility } from "@/Components/HasAbility";
import { Button } from "@/Components/ui/button";
import { PlusIcon } from "lucide-react";
import { DottedSeparator } from "@/Components/DottedSeparator";
import { toast } from "sonner";

interface RolePermission {
    id: string;
    name: string;
    permissions: string[];
    [key: string]: boolean | string | string[];
}
type Permissions = "add" | "edit" | "view" | "delete" | "all";
type FormData = {
    id: number | null;
    name: string | undefined;
    roles: Record<number, Array<Permissions>>;
};

interface CreateRoleProps {
    rolePermissions: RolePermission[];
}

export default function CreateRole({ rolePermissions }: CreateRoleProps) {
    const authUser = usePage().props.auth.user;
    const { data, setData, post, errors, reset, processing } =
        useForm<FormData>();

    const isChecked = (id: number, type: Permissions) => {
        // Ensure data.roles exists and contains an entry for the given `id`
        const roles = data.roles ? data.roles[id] : undefined;

        if (type === "all") {
            // Check if `roles` exists and compare its length to permissions length
            const rolePermissionsForId = rolePermissions[id - 1];
            if (rolePermissionsForId && roles) {
                return roles.length === rolePermissionsForId.permissions.length;
            }
            return false;
        }

        // Ensure `data.roles` and `data.roles[id]` are properly defined before checking
        if (roles && roles.includes(type)) {
            return true;
        }

        return false;
    };

    const handleSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { value, dataset, checked } = e.target;
        const id = parseInt(dataset.id || "0", 10);
        if (!id || !value) return; // Exit if `id` or `value` is invalid

        const updatedRoles = { ...data.roles }; // Create a shallow copy of roles

        if (checked) {
            // Add permission to roles
            if (!updatedRoles[id]) {
                updatedRoles[id] =
                    value !== "view"
                        ? ["view", value as Permissions]
                        : [value as Permissions];
            } else {
                const newPermissions = new Set([...updatedRoles[id], value]);
                updatedRoles[id] = Array.from(newPermissions) as Permissions[];
            }
        } else {
            // Remove permission from roles
            if (updatedRoles[id]) {
                updatedRoles[id] = updatedRoles[id].filter(
                    (permission: string) => permission !== value
                );
                if (updatedRoles[id].length === 0) {
                    delete updatedRoles[id]; // Remove role entirely if no permissions remain
                }
            }
        }

        setData("roles", updatedRoles);
    };

    const renderPermissionCheckbox = (
        rolePermission: RolePermission,
        permission: string
    ) => {
        return rolePermission.permissions.includes(permission) ? (
            <Checkbox
                data-id={rolePermission.id.toString()}
                value={permission}
                onChange={handleSelect}
                className="h-5 w-5"
                checked={isChecked(
                    Number(rolePermission.id),
                    permission as Permissions
                )}
            />
        ) : (
            <span className="text-gray-500">N/A</span>
        );
    };
    const handleSelectAll = (e: React.ChangeEvent<HTMLInputElement>) => {
        const roles = { ...data.roles }; // Create a shallow copy of `data.roles` to avoid mutating state directly
        const { dataset, checked } = e.target;
        const id = dataset.id ? parseInt(dataset.id, 10) : 0;

        // Ensure that roles[id] exists before assigning
        if (id > 0) {
            if (checked) {
                // Initialize the roles[id] array if it doesn't exist
                if (!roles[id]) {
                    roles[id] = [];
                }

                // Assign all permissions to this role
                roles[id] = rolePermissions[id - 1]
                    ?.permissions as Permissions[];
            } else {
                // Deselect all permissions for the role
                delete roles[id];
            }
        }

        setData("roles", roles);
    };

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
                            <div className="w-full lg:w-auto">
                                <TextInput
                                    type="text"
                                    placeholder="Role Name"
                                    className="mt-1 block w-full"
                                    onChange={(e) => {
                                        setData("name", e.target.value);
                                    }}
                                />
                            </div>
                            <HasAbility user={authUser} check="add">
                                <Button
                                    className="w-full lg:w-auto"
                                    size="sm"
                                    onClick={handleSubmit}
                                >
                                    <PlusIcon className="size-4 mr-2" />
                                    Save
                                </Button>
                            </HasAbility>
                        </div>
                        <DottedSeparator className="my-4" />
                        <Table>
                            {/* Table Header */}
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>All</TableHead>
                                    <TableHead>Add</TableHead>
                                    <TableHead>View</TableHead>
                                    <TableHead>Edit</TableHead>
                                    <TableHead>Delete</TableHead>
                                </TableRow>
                            </TableHeader>

                            {/* Table Body */}
                            <TableBody>
                                {rolePermissions &&
                                    rolePermissions.map(
                                        (rolePermission, index) => (
                                            <TableRow key={index}>
                                                <TableCell>
                                                    {rolePermission.id}
                                                </TableCell>
                                                <TableCell>
                                                    {rolePermission.name}
                                                </TableCell>
                                                <TableCell>
                                                    <Checkbox
                                                        checked={isChecked(
                                                            Number(
                                                                rolePermission.id
                                                            ),
                                                            "all"
                                                        )}
                                                        onChange={
                                                            handleSelectAll
                                                        }
                                                        className="h-5 w-5"
                                                        data-id={rolePermission.id.toString()}
                                                    />
                                                </TableCell>
                                                <TableCell>
                                                    {renderPermissionCheckbox(
                                                        rolePermission,
                                                        "add"
                                                    )}
                                                </TableCell>
                                                <TableCell>
                                                    {renderPermissionCheckbox(
                                                        rolePermission,
                                                        "view"
                                                    )}
                                                </TableCell>
                                                <TableCell>
                                                    {renderPermissionCheckbox(
                                                        rolePermission,
                                                        "edit"
                                                    )}
                                                </TableCell>
                                                <TableCell>
                                                    {renderPermissionCheckbox(
                                                        rolePermission,
                                                        "delete"
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        )
                                    )}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>
        </AdminAuthenticatedLayout>
    );
}
