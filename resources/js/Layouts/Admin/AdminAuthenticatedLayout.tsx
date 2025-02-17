import { Navbar } from "@/Components/Navbar";
import { Sidebar } from "@/Components/Sidebar";
import { CreateUserModal } from "@/features/admin/users/components/create-user-modal";
import { EditUserModal } from "@/features/admin/users/components/edit-user-modal";
import { PropsWithChildren, ReactNode } from "react";
import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import { PageProps } from "@/types";
import { toast } from "sonner";

export default function AdminAuthenticated({
    children,
}: PropsWithChildren<{ children: ReactNode }>) {
    const { flash, errors } = usePage<PageProps>().props;
    const formErrors = Object.keys(errors).length;

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        if (formErrors > 0) {
            toast.error("There are " + formErrors + " form errors.");
        }
    }, [flash, errors]);
    return (
        <div className="min-h-screen">
            <CreateUserModal />
            <EditUserModal />
            <div className="flex w-full h-full">
                <div className="fixed left-0 top-0 hidden lg:block lg:w-[264px] h-full overflow-y-auto">
                    <Sidebar />
                </div>
                <div className="lg:pl-[264px] w-full">
                    <div className="mx-auto max-w-screen-2xl">
                        <Navbar />
                    </div>
                    <main className="h-full py-8 px-6 flex flex-col">
                        {children}
                    </main>
                </div>
            </div>
        </div>
    );
}
