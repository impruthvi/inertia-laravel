import { Head, router, usePage } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";
import { useState } from "react";
import { SortingState } from "@tanstack/react-table";

import { PaginatedData, User } from "@/types";
import { Button } from "@/Components/ui/button";
import { DottedSeparator } from "@/Components/DottedSeparator";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DataTable } from "@/features/admin/users/components/data-table";
import { columns } from "@/features/admin/users/components/columns";
import { useCreateUserModal } from "@/features/admin/users/hooks/use-create-user-modal";
import useDebounce from "@/hooks/useDebounce";
import TextInput from "@/Components/TextInput";

type Filter = {
    search: string;
    sort: SortingState;
};

export default function UsersPage({ users }: { users: PaginatedData<User> }) {
    const { open: createUser } = useCreateUserModal();
    const { query } = usePage().props.ziggy;

    // Initialize filters from URL query parameters
    const [filter, setFilter] = useState<Filter>({
        search: (query.search as string) ?? "",
        sort: Object.entries(query.sort ?? {}).map(([id, direction]) => ({
            id,
            desc: direction === "desc",
        })),
    });

    // Helper function to build URL parameters from current filters
    const buildParams = (currentFilter: Filter) => {
        const params: Record<string, string> = {};

        if (currentFilter.search) {
            params.search = currentFilter.search;
        }

        currentFilter.sort.forEach((sort) => {
            params[`sort[${sort.id}]`] = sort.desc ? "desc" : "asc";
        });

        // Keep the current page from URL if it exists
        if (query.page) {
            params.page = query.page as string;
        }

        return params;
    };

    // Helper function to update route with new parameters
    const updateRoute = (params: Record<string, string>) => {
        router.get(route("admin.users.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle sort changes
    const handleSortChange = (sorting: SortingState) => {
        const newFilter = { ...filter, sort: sorting };
        setFilter(newFilter);
        updateRoute(buildParams(newFilter));
    };

    // Handle search changes (debounced)
    const handleSearchChange = () => {
        const params = buildParams(filter);
        // Reset page when searching
        delete params.page;
        updateRoute(params);
    };

    useDebounce(filter.search, 500, handleSearchChange);

    const { data, ...pagination } = users;

    return (
        <AdminAuthenticatedLayout>
            <Head title="Users" />
            <div className="h-full flex flex-col">
                <div className="flex-1 w-full border rounded-lg">
                    <div className="h-full flex flex-col overflow-auto p-4">
                        <div className="flex flex-col gap-y-2 lg:flex-row justify-between items-center">
                            <div className="w-full lg:w-auto">
                                <TextInput
                                    type="text"
                                    placeholder="Search ..."
                                    className="mt-1 block w-full"
                                    onChange={(e) => {
                                        setFilter((prev) => ({
                                            ...prev,
                                            search: e.target.value,
                                        }));
                                    }}
                                    value={filter.search}
                                />
                            </div>
                            <Button
                                className="w-full lg:w-auto"
                                size="sm"
                                onClick={createUser}
                            >
                                <PlusIcon className="size-4 mr-2" />
                                New
                            </Button>
                        </div>
                        <DottedSeparator className="my-4" />
                        <DataTable
                            data={data ?? []}
                            columns={columns}
                            pagination={pagination}
                            onSortChange={handleSortChange}
                        />
                    </div>
                </div>
            </div>
        </AdminAuthenticatedLayout>
    );
}
