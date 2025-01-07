import AdminAuthenticatedLayout from "@/Layouts/Admin/AdminAuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function Dashboard() {
    //
    return (
        <AdminAuthenticatedLayout>
            <Head title="Dashboard" />

            <div className="h-full flex flex-col space-y-4">
                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div className="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </AdminAuthenticatedLayout>
    );
}
