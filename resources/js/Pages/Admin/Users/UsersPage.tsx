import { Head } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";

import { User } from "@/types";

import { Button } from "@/Components/ui/button";
import { DottedSeparator } from "@/Components/DottedSeparator";
import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { DataTable } from "@/features/admin/users/components/data-table";
import { columns } from "@/features/admin/users/components/columns";


export default function UsersPage({ users }: { users: User[] }) {
    return (
        <AdminAuthenticatedLayout>
            <Head title="Users" />
            <div className="h-full flex flex-col">  
                <div className="flex-1 w-full border rounded-lg">
                    <div className="h-full flex flex-col overflow-auto p-4">
                        <div className="flex flex-col gap-y-2 lg:flex-row justify-between items-center">
                            <div className="w-full lg:w-auto">Bread Crumbs</div>
                            <Button
                                className="w-full lg:w-auto"
                                size="sm"
                                onClick={() => {}}
                            >
                                <PlusIcon className="size-4 mr-2" />
                                New
                            </Button>
                        </div>
                        <DottedSeparator className="my-4" />
                        <DataTable data={users ?? []} columns={columns}/>
                    </div>
                </div>
            </div>
        </AdminAuthenticatedLayout>
    );
}
