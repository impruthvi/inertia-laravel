import { Head, router } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";

import { PaginatedData, User } from "@/types";

import { Button } from "@/Components/ui/button";
import { DottedSeparator } from "@/Components/DottedSeparator";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DataTable } from "@/features/admin/users/components/data-table";
import { columns } from "@/features/admin/users/components/columns";
import { useCreateUserModal } from "@/features/admin/users/components/hooks/use-create-user-modal";
import { SortingState } from "@tanstack/react-table";
import { Input } from "@headlessui/react";
import { useState } from "react";

export default function UsersPage({ users }: { users: PaginatedData<User> }) {
    const { open: createUser } = useCreateUserModal();
    const [search, setSearch] = useState("");
    const [sorting, setSorting] = useState<SortingState>([]);

    const { data, ...pagination } = users;
    const handleSortChange = (sorting: SortingState) => {
        setSorting(sorting);
        // Handle sorting changes here, e.g., make API calls
        const params = sorting.reduce((acc, curr) => {
            acc[`sort[${curr.id}]`] = curr.desc ? "desc" : "asc";
            return acc;
        }, {} as Record<string, string>);

        // Include search in the parameters
        if (search) {
            params.search = search;
        }

        // Make API call here
        router.get(route("admin.users.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        // Handle search changes here, e.g., make API calls
        const searchValue = e.target.value;
        setSearch(searchValue);
        const params: Record<string, string> = { search: searchValue };

        // Include sorting in the parameters
        if (sorting.length > 0) {
            sorting.forEach((sort) => {
                params[`sort[${sort.id}]`] = sort.desc ? "desc" : "asc";
            });
        }

        // Make API call here
        router.get(route("admin.users.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AdminAuthenticatedLayout>
            <Head title="Users" />
            <div className="h-full flex flex-col">
                <div className="flex-1 w-full border rounded-lg">
                    <div className="h-full flex flex-col overflow-auto p-4">
                        <div className="flex flex-col gap-y-2 lg:flex-row justify-between items-center">
                            <div className="w-full lg:w-auto">
                                <Input
                                    type="text"
                                    placeholder="Search users"
                                    onChange={handleSearchChange}
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
