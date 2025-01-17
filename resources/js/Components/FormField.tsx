import InputError from "./InputError";
import InputLabel from "./InputLabel";
import TextInput from "./TextInput";

interface FormFieldProps {
    label: string;
    name: string;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    error?: string;
    type?: string;
}

export const FormField: React.FC<FormFieldProps> = ({
    label,
    name,
    value,
    onChange,
    error,
    type = "text",
}) => {
    return (
        <div className="flex flex-col space-y-2">
            <InputLabel
                htmlFor={name}
                value={label}
                className="font-medium text-gray-700"
            />
            <TextInput
                id={name}
                type={type}
                name={name}
                value={value}
                onChange={onChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
            {error && <InputError message={error} className="text-sm" />}
        </div>
    );
};
