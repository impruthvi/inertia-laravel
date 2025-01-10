import axios from "axios";

export const getUserById = async (userId: string) => {
    try {
        const { data } = await axios.get(route("admin.users.edit", userId));
        return data;
    } catch (e) {
        return { error: true };
    }
};
