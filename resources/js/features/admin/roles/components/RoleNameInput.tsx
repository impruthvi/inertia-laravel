import React from "react";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";

interface RoleNameInputProps {
    value: string;
    onChange: (value: string) => void;
    error?: string;
}

export const RoleNameInput: React.FC<RoleNameInputProps> = ({
    value,
    onChange,
    error,
}) => (
    <div className="w-full lg:w-auto">
        <TextInput
            type="text"
            placeholder="Role Name"
            className="mt-1 block w-full"
            value={value}
            onChange={(e) => onChange(e.target.value)}
        />

        <InputError message={error} className="mt-2" />
    </div>
);
