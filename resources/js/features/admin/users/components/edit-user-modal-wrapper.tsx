"use client";

import { Loader } from "lucide-react";
import { Card, CardContent } from "@/Components/ui/card";
import { useGetUserById } from "../api/use-get-user";
import { EditUserForm } from "./edit-user-form";

interface EditUserWrapperProps {
    onCancel: () => void;
    id: string;
}

export const EditUserWrapper = ({ onCancel, id }: EditUserWrapperProps) => {
    const { data: initialValues, isLoading: isLoadingUser } = useGetUserById({
        userId: id,
    });

    const isLoading = isLoadingUser;

    if (isLoading) {
        return (
            <Card className="w-full h-[400px] border-none shadow-none">
                <CardContent className="flex items-center justify-center h-full">
                    <Loader className="size-5 animate-spin text-muted-foreground" />
                </CardContent>
            </Card>
        );
    }

    if (!initialValues) return null;

    return (
        <div className="">
            <EditUserForm onCancel={onCancel} initialValues={initialValues} />
        </div>
    );
};
