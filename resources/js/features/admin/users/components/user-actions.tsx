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
import { useEditUserModal } from "./hooks/use-edit-user-modal";

interface TaskActionsProps {
    id: number;
    children: React.ReactNode;
}

export const UserActions = ({ id, children }: TaskActionsProps) => {
    const { open } = useEditUserModal();

    return (
        <div className="flex justify-end">
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
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};
