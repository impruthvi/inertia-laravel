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
