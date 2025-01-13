import { useForm } from "@inertiajs/react";
import { FormData, Permission, RolePermission } from "../types/role";

export const useRoleManagement = (initialRolePermissions: RolePermission[]) => {
    const { data, setData, post, errors, reset, processing } =
        useForm<FormData>({
            id: null,
            name: "",
            roles: {},
        });

    const isChecked = (id: number, type: Permission | "all") => {
        const roles = data.roles[id];
        const rolePermission = initialRolePermissions[id - 1];

        if (type === "all") {
            if (!roles || !rolePermission) return false;
            return rolePermission.permissions.every((permission) =>
                roles.includes(permission)
            );
        }

        return roles?.includes(type) ?? false;
    };

    const hasOtherPermissions = (id: number): boolean => {
        const roles = data.roles[id];
        if (!roles) return false;

        return roles.some((permission) => permission !== "view");
    };

    const handleSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { value, dataset, checked } = e.target;
        const id = parseInt(dataset.id || "0", 10);
        if (!id || !value) return;

        const updatedRoles = { ...data.roles };
        const permission = value as Permission;
        const rolePermission = initialRolePermissions[id - 1];

        if (checked) {
            // Initialize the array if it doesn't exist
            if (!updatedRoles[id]) {
                updatedRoles[id] = [];
            }

            // If selecting a non-view permission, ensure view is selected
            if (permission !== "view") {
                updatedRoles[id] = Array.from(
                    new Set([
                        ...updatedRoles[id],
                        "view" as Permission,
                        permission,
                    ])
                );
            } else {
                // If selecting view permission
                updatedRoles[id] = Array.from(
                    new Set([...updatedRoles[id], permission])
                );
            }

            // Check if all permissions are now selected
            const currentPermissions = new Set(updatedRoles[id]);
            const allAvailableSelected = rolePermission.permissions.every((p) =>
                currentPermissions.has(p)
            );

            if (allAvailableSelected) {
                updatedRoles[id] = rolePermission.permissions;
            }
        } else {
            // When unchecking a permission
            if (permission === "view") {
                // Only allow unchecking view if no other permissions are selected
                if (!hasOtherPermissions(id)) {
                    delete updatedRoles[id];
                }
            } else {
                // Remove only the unchecked permission, keep "view"
                updatedRoles[id] = (updatedRoles[id] || []).filter(
                    (p) => p === "view" || p !== permission
                );
            }
        }

        setData("roles", updatedRoles);
    };

    const handleSelectAll = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { dataset, checked } = e.target;
        const id = parseInt(dataset.id || "0", 10);
        if (!id) return;

        const updatedRoles = { ...data.roles };
        const rolePermission = initialRolePermissions[id - 1];

        if (checked && rolePermission) {
            updatedRoles[id] = rolePermission.permissions;
        } else {
            // When unchecking "all", keep only the "view" permission
            updatedRoles[id] = ["view" as Permission];
        }

        setData("roles", updatedRoles);
    };

    return {
        data,
        setData,
        isChecked,
        hasOtherPermissions,
        handleSelect,
        handleSelectAll,
        post,
        errors,
        reset,
        processing,
    };
};
