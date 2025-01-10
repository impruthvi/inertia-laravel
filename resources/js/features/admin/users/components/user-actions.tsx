import React from "react";

import {
    ExternalLink,
    ExternalLinkIcon,
    PencilIcon,
    TrashIcon,
} from "lucide-react";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import { useEditUserModal } from "../hooks/use-edit-user-modal";
import { useConfirm } from "@/hooks/use-confirm";
import { router } from "@inertiajs/react";
import { toast } from "sonner";

interface TaskActionsProps {
    id: string;
    children: React.ReactNode;
}

export const UserActions = ({ id, children }: TaskActionsProps) => {
    const { open } = useEditUserModal();
    const [ConfirmDialog, confirm] = useConfirm(
        "Delete user",
        "This action cannot be undone",
        "destructive"
    );

    const onDelete = async () => {
        const ok = await confirm();
        if (!ok) return;

        router.delete(route("admin.users.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("User deleted successfully");
            },
        });

    };

    return (
        <div className="flex justify-end">
            <ConfirmDialog />
            <DropdownMenu modal={false}>
                <DropdownMenuTrigger asChild>{children}</DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-48">
                    <DropdownMenuItem
                        onClick={() => open(id)}
                        className="font-medium p-[10px]"
                    >
                        <PencilIcon className="size-4 mr-2 stroke-2" />
                        Edit User
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        onClick={onDelete}
                        // disabled={isPending}
                        className="text-amber-700 focus:text-amber-700 font-medium p-[10px]"
                    >
                        <TrashIcon className="size-4 mr-2 stroke-2" />
                        Delete User
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};
