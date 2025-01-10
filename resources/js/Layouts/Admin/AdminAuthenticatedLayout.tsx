import { Navbar } from "@/Components/Navbar";
import { Sidebar } from "@/Components/Sidebar";
import { CreateUserModal } from "@/features/admin/users/components/create-user-modal";
import { EditUserModal } from "@/features/admin/users/components/edit-user-modal";
import { PropsWithChildren, ReactNode } from "react";

export default function AdminAuthenticated({
    children,
}: PropsWithChildren<{ children: ReactNode }>) {
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
