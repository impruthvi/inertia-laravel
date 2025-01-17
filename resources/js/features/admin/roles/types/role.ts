export type Permission = "add" | "edit" | "view" | "delete";

export interface RolePermission {
    id: string;
    name: string;
    permissions: Permission[];
}

export interface FormData {
    id: number | null;
    display_name: string;
    roles: Record<number, Permission[]>;
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    display_name: string;
    is_common_role: boolean;
    created_by: string;
    updated_by: string;
    created_at: string;
    updated_at: string;
}
