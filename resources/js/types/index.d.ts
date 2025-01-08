export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
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
