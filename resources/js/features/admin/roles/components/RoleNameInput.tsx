import React from "react";
import TextInput from "@/Components/TextInput";

interface RoleNameInputProps {
    value: string;
    onChange: (value: string) => void;
}

export const RoleNameInput: React.FC<RoleNameInputProps> = ({
    value,
    onChange,
}) => (
    <div className="w-full lg:w-auto">
        <TextInput
            type="text"
            placeholder="Role Name"
            className="mt-1 block w-full"
            value={value}
            onChange={(e) => onChange(e.target.value)}
        />
    </div>
);
