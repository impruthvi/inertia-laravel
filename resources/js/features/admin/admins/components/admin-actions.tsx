import React from "react";

import { PencilIcon, TrashIcon } from "lucide-react";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import { useConfirm } from "@/hooks/use-confirm";
import { router, usePage } from "@inertiajs/react";
import { HasAbility } from "@/Components/HasAbility";

interface AdminActionsProps {
    id: number;
    children: React.ReactNode;
}

export const AdminActions = ({ id, children }: AdminActionsProps) => {
    const authUser = usePage().props.auth.user;
    const [ConfirmDialog, confirm] = useConfirm(
        "Delete admin",
        "This action cannot be undone",
        "destructive"
    );

    const onDelete = async () => {
        const ok = await confirm();
        if (!ok) return;

        router.delete(route("admin.admins.destroy", id), {
            preserveScroll: true,
        });
    };

    return (
        <div className="flex justify-end">
            <ConfirmDialog />
            <DropdownMenu modal={false}>
                <DropdownMenuTrigger asChild>{children}</DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-48">
                    <HasAbility user={authUser} check="edit">
                        <DropdownMenuItem
                            onClick={() => {
                                router.get(route("admin.admins.edit", id));
                            }}
                            className="font-medium p-[10px]"
                        >
                            <PencilIcon className="size-4 mr-2 stroke-2" />
                            Edit Admin
                        </DropdownMenuItem>
                    </HasAbility>
                    <HasAbility user={authUser} check="delete">
                        <DropdownMenuItem
                            onClick={onDelete}
                            className="text-amber-700 focus:text-amber-700 font-medium p-[10px]"
                        >
                            <TrashIcon className="size-4 mr-2 stroke-2" />
                            Delete Admin
                        </DropdownMenuItem>
                    </HasAbility>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};
