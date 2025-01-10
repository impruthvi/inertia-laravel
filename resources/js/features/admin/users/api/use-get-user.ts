import { useQuery } from "@tanstack/react-query";
import { getUserById } from "../server";

interface UseGetUserProps {
    userId: string;
}

export const useGetUserById = ({ userId }: UseGetUserProps) => {
    const query = useQuery({
        queryKey: ["user", userId],
        queryFn: async () => {
            const response = await getUserById(userId);

            if (response.error) {
                throw new Error("Failed to fetch user");
            }

            return response.data;
        },
    });

    return query;
};
