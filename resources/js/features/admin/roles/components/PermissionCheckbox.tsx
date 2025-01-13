import React from "react";
import Checkbox from "@/Components/Checkbox";
import { Permission, RolePermission } from "../types/role";

interface PermissionCheckboxProps {
    rolePermission: RolePermission;
    permission: Permission;
    isChecked: (id: number, type: Permission | "all") => boolean;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    hasOtherPermissions: (id: number) => boolean;
}

export const PermissionCheckbox: React.FC<PermissionCheckboxProps> = ({
    rolePermission,
    permission,
    isChecked,
    onChange,
    hasOtherPermissions,
}) => {
    if (!rolePermission.permissions.includes(permission)) {
        return <span className="text-gray-500">N/A</span>;
    }

    const id = Number(rolePermission.id);
    const isViewPermission = permission === "view";

    return (
        <Checkbox
            data-id={rolePermission.id}
            value={permission}
            onChange={onChange}
            className="h-5 w-5"
            checked={isChecked(id, permission)}
            disabled={isViewPermission && hasOtherPermissions(id)}
        />
    );
};
