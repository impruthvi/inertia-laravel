import React from "react";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
import { PERMISSIONS } from "../constants/permissions";
import { PermissionCheckbox } from "./PermissionCheckbox";
import Checkbox from "@/Components/Checkbox";
import { RolePermission } from "../types/role";

interface RolePermissionsTableProps {
    rolePermissions: RolePermission[];
    isChecked: (id: number, type: any) => boolean;
    onSelect: (e: React.ChangeEvent<HTMLInputElement>) => void;
    onSelectAll: (e: React.ChangeEvent<HTMLInputElement>) => void;
    hasOtherPermissions: (id: number) => boolean;
}

export const RolePermissionsTable: React.FC<RolePermissionsTableProps> = ({
    rolePermissions,
    isChecked,
    onSelect,
    onSelectAll,
    hasOtherPermissions,
}) => (
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>ID</TableHead>
                <TableHead>Title</TableHead>
                <TableHead>All</TableHead>
                {PERMISSIONS.map((permission) => (
                    <TableHead key={permission}>
                        {permission.charAt(0).toUpperCase() +
                            permission.slice(1)}
                    </TableHead>
                ))}
            </TableRow>
        </TableHeader>
        <TableBody>
            {rolePermissions.map((rolePermission) => (
                <TableRow key={rolePermission.id}>
                    <TableCell>{rolePermission.id}</TableCell>
                    <TableCell>{rolePermission.name}</TableCell>
                    <TableCell>
                        <Checkbox
                            checked={isChecked(
                                Number(rolePermission.id),
                                "all"
                            )}
                            onChange={onSelectAll}
                            className="h-5 w-5"
                            data-id={rolePermission.id}
                        />
                    </TableCell>
                    {PERMISSIONS.map((permission) => (
                        <TableCell key={permission}>
                            <PermissionCheckbox
                                rolePermission={rolePermission}
                                permission={permission}
                                isChecked={isChecked}
                                onChange={onSelect}
                                hasOtherPermissions={hasOtherPermissions}
                            />
                        </TableCell>
                    ))}
                </TableRow>
            ))}
        </TableBody>
    </Table>
);
