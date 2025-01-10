"use client";

import { FormEventHandler } from "react";

import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";

import { DottedSeparator } from "@/Components/DottedSeparator";
import { Button } from "@/Components/ui/button";
import { cn } from "@/lib/utils";
import { useForm } from "@inertiajs/react";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import { User } from "@/types";
import { toast } from "sonner";

interface EditUserFormProps {
    onCancel?: () => void;
    initialValues: User;
}

export const EditUserForm = ({ onCancel,initialValues }: EditUserFormProps) => {
    const { data, setData, patch, processing, errors, reset } = useForm({
        name: initialValues.name,
        email: initialValues.email,
    });

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route("admin.users.update", initialValues.id), {
            onSuccess: () => {
                reset();
                onCancel && onCancel();
                toast.success("User updated successfully");
            },
            onError: () => {
                toast.error("Failed to update user");
            }
        });
    };

    return (
        <Card className="w-full h-full border-none shadow-none">
            <CardHeader className="flex p-7">
                <CardTitle className="text-xl font-bold">
                    Edit an user
                </CardTitle>
            </CardHeader>
            <div className="px-7">
                <DottedSeparator />
            </div>
            <CardContent className="p-7">
                <form onSubmit={onSubmit}>
                    <div className="flex flex-col gap-y-4">
                        <div className="">
                            <InputLabel htmlFor="name" value="Name" />
                            <TextInput
                                id="name"
                                name="name"
                                value={data.name}
                                className="mt-1 block w-full"
                                autoComplete="name"
                                isFocused={true}
                                onChange={(e) =>
                                    setData("name", e.target.value)
                                }
                                required
                            />
                            <InputError
                                message={errors.name}
                                className="mt-2"
                            />
                        </div>
                        <div className="">
                            <InputLabel htmlFor="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="mt-1 block w-full"
                                autoComplete="username"
                                onChange={(e) =>
                                    setData("email", e.target.value)
                                }
                                required
                            />
                            <InputError
                                message={errors.email}
                                className="mt-2"
                            />
                        </div>
                    </div>
                    <DottedSeparator className="py-7" />
                    <div className="flex items-center justify-between">
                        <Button
                            type="button"
                            size="lg"
                            variant="secondary"
                            onClick={onCancel}
                            disabled={processing}
                            className={cn(!onCancel && "invisible")}
                        >
                            Cancel
                        </Button>

                        <Button type="submit" size="lg" variant="primary">
                            Update User
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
};
