import { useQueryState, parseAsInteger} from "nuqs";

export const useEditUserModal = () => {
    const [userId, setUserId] = useQueryState("edit-user", parseAsInteger);

    const open = (id: number) => setUserId(id);
    const close = () => setUserId(null);

    return {
        open,
        userId,
        close,
        setUserId,
    };
};
