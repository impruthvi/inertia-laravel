export interface User {
    id: number;
    first_name: string;
    last_name: string;
    name: string;
    role: string;
    email: string;
    email_verified_at: string;
    access_permissions: Array<string>;
    trashed_role: Role;
    role_permission: Role | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
    ziggy: {
        location: string;
        query: {
            [key: string]: string | string[] | object | undefined;
        };
    };
};

export type PaginatedData<T> = {
    data: T[];
    links: PaginationItem[];
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string;
    prev_page_url: string;
};
interface PaginationItem {
    url: null | string;
    label: string;
    active: boolean;
}
export type Pagination = Omit<PaginatedData<unknown>, "data">;
