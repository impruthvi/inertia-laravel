import { Head, router, usePage } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";

import { PaginatedData, User } from "@/types";

import { Button } from "@/Components/ui/button";
import { DottedSeparator } from "@/Components/DottedSeparator";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DataTable } from "@/features/admin/users/components/data-table";
import { columns } from "@/features/admin/users/components/columns";
import { useCreateUserModal } from "@/features/admin/users/components/hooks/use-create-user-modal";
import { SortingState } from "@tanstack/react-table";
import { useState } from "react";
import useDebounce from "@/hooks/useDebounce";
import TextInput from "@/Components/TextInput";

type Filter = {
    search: string;
    sort: { id: string; desc: boolean }[];
};

export default function UsersPage({ users }: { users: PaginatedData<User> }) {
    const { open: createUser } = useCreateUserModal();
    const { query } = usePage().props.ziggy;

    const [filter, setFilter] = useState<Filter>({
        search: (query.search as string) ?? "",
        sort: Array.isArray(query.sort) ? query.sort : [],
    });

    const { data, ...pagination } = users;
    const handleSortChange = (sorting: SortingState) => {
        setFilter((prev) => ({
            ...prev,
            sort: sorting,
        }));

        // Handle sorting changes here, e.g., make API calls
        const params = sorting.reduce((acc, curr) => {
            acc[`sort[${curr.id}]`] = curr.desc ? "desc" : "asc";
            return acc;
        }, {} as Record<string, string>);

        // Include search in the parameters
        if (filter.search) {
            params.search = filter.search;
        }

        // Make API call here
        router.get(route("admin.users.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSearchChange = () => {

        const params: Record<string, string> = { search: filter.search };

        // Include sorting in the parameters
        if (filter.sort.length > 0) {
            filter.sort.forEach((sort) => {
                params[`sort[${sort.id}]`] = sort.desc ? "desc" : "asc";
            });
        }

        if (!filter.search) {
            delete params.search;
        }

        // Make API call here
        router.get(route("admin.users.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    useDebounce(filter.search, 500, handleSearchChange);

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
