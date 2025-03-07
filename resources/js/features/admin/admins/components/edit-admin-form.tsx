import { FormField } from "@/Components/FormField";
import InputError from "@/Components/InputError";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { Role } from "../../roles/types/role";
import { router } from "@inertiajs/react";
import { Admin } from "../types/admin";

interface AdminEditFormComponentProps {
    data: any;
    setData: (key: string, value: any) => void;
    errors: Record<string, string>;
    roles: Role[];
    role: Role;
    admin: Admin
}

export const AdminEditForm: React.FC<AdminEditFormComponentProps> = ({
    data,
    setData,
    errors,
    roles,
    role,
    admin,
}) => {
    const handleOnChange = (value: string) => {
        console.log(value);
        
        setData("role_id", value);

        router.visit(
            route("admin.admins.edit", {
                admin: admin.id,
                role: value,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                only: ["selected_permissions"],
            }
        );
    };

    const selectedRoleId = role?.id ? String(role.id) : String(admin.role_id) || "";


    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <FormField
                label="First Name"
                name="first_name"
                value={data.first_name}
                onChange={(e) => setData("first_name", e.target.value)}
                error={errors.first_name}
            />

            <FormField
                label="Last Name"
                name="last_name"
                value={data.last_name}
                onChange={(e) => setData("last_name", e.target.value)}
                error={errors.last_name}
            />

            <FormField
                label="Email Address"
                name="email"
                type="email"
                value={data.email}
                onChange={(e) => setData("email", e.target.value)}
                error={errors.email}
            />

            <div className="flex flex-col space-y-2">
                <label
                    htmlFor="role"
                    className="text-sm font-medium text-gray-700"
                >
                    Role
                </label>
                <Select
                    defaultValue={selectedRoleId}
                    onValueChange={handleOnChange}
                >
                    <SelectTrigger className="w-full">
                        <SelectValue placeholder="Select a role" />
                    </SelectTrigger>
                    <SelectContent>
                        {roles?.map((role) => (
                            <SelectItem key={role.id} value={String(role.id)}>
                                {role.display_name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                {errors.role_id && (
                    <InputError message={errors.role_id} className="mt-1" />
                )}
            </div>
        </div>
    );
};
