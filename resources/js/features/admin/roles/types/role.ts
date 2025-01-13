export type Permission = "add" | "edit" | "view" | "delete";

export interface RolePermission {
    id: string;
    name: string;
    permissions: Permission[];
}

export interface FormData {
    id: number | null;
    name: string;
    roles: Record<number, Permission[]>;
}
