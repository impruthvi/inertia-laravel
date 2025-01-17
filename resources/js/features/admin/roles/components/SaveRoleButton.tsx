import React from "react";
import { Button } from "@/Components/ui/button";
import { PlusIcon } from "lucide-react";
import { HasAbility } from "@/Components/HasAbility";

interface SaveRoleButtonProps {
    user: any;
    onClick: () => void;
    processing: boolean;
    isUpdate?: boolean;
}

export const SaveRoleButton: React.FC<SaveRoleButtonProps> = ({
    user,
    onClick,
    processing,
    isUpdate = false,
}) => (
    <HasAbility user={user} check="add">
        <Button
            className="w-full lg:w-auto"
            size="sm"
            onClick={onClick}
            disabled={processing}
        >
            <PlusIcon className="size-4 mr-2" />
            {isUpdate ? "Update" : "Save"}
            {processing && "..."}
        </Button>
    </HasAbility>
);
