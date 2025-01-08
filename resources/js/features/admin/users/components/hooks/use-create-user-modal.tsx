import { useQueryState, parseAsBoolean } from "nuqs";

export const useCreateUserModal = () => {
    const [isOpen, setIsOpen] = useQueryState(
        "create-user",
        parseAsBoolean.withDefault(false).withOptions({ clearOnDefault: true })
    );

    const open = () => setIsOpen(true);
    const close = () => setIsOpen(false);

    return {
        isOpen,
        open,
        close,
        setIsOpen,
    };
};
