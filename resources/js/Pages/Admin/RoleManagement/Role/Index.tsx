import { Head, router, usePage } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";
import { useState } from "react";
import { SortingState } from "@tanstack/react-table";

import { PaginatedData } from "@/types";
import { Button } from "@/Components/ui/button";
import { DottedSeparator } from "@/Components/DottedSeparator";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DataTable } from "@/features/admin/roles/components/data-table";
import { columns } from "@/features/admin/roles/components/columns";
import useDebounce from "@/hooks/useDebounce";
import TextInput from "@/Components/TextInput";
import { HasAbility } from "@/Components/HasAbility";
import { Role } from "@/features/admin/roles/types/role";

type Filter = {
    search: string;
    sort: SortingState;
};

export default function RoleIndex({ roles }: { roles: PaginatedData<Role> }) {
    const { query } = usePage().props.ziggy;
    const authUser = usePage().props.auth.user;
    const currentPage = Number(query.page) || 1;

    // Initialize filters from URL query parameters
    const [filter, setFilter] = useState<Filter>({
        search: (query.search as string) ?? "",
        sort: Object.entries(query.sort ?? {}).map(([id, direction]) => ({
            id,
            desc: direction === "desc",
        })),
    });

    // Helper function to build URL parameters from current filters
    const buildParams = (currentFilter: Filter, preservePage = true) => {
        const params: Record<string, string> = {};

        if (currentFilter.search) {
            params.search = currentFilter.search;
        }

        currentFilter.sort.forEach((sort) => {
            
            params[`sort[${sort.id}]`] = sort.desc ? "desc" : "asc";
        });

        // Always preserve the current page unless explicitly told not to
        if (preservePage && currentPage > 1) {
            params.page = currentPage.toString();
        }

        return params;
    };

    // Helper function to update route with new parameters
    const updateRoute = (params: Record<string, string>) => {
        router.get(route("admin.roles.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle sort changes
    const handleSortChange = (sorting: SortingState) => {
        const newFilter = { ...filter, sort: sorting };
        setFilter(newFilter);
        // Preserve page on sort
        updateRoute(buildParams(newFilter, false));
    };

    // Handle search changes (debounced)
    const handleSearchChange = () => {
        // Don't preserve page on search - reset to page 1
        updateRoute(buildParams(filter, false));
    };

    useDebounce(filter.search, 500, handleSearchChange);

    const { data, ...pagination } = roles;

    return (
        <AdminAuthenticatedLayout>
            <Head title="Roles" />
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
                            <HasAbility user={authUser} check="add">
                                <Button
                                    className="w-full lg:w-auto"
                                    size="sm"
                                    onClick={() => {
                                        router.get(route("admin.roles.create"));
                                    }}
                                >
                                    <PlusIcon className="size-4 mr-2" />
                                    New
                                </Button>
                            </HasAbility>
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
