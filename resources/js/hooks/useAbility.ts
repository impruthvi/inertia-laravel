import { useMemo } from "react";
import { User } from "@/types";

const ACTIONS = ["add", "edit", "view", "delete"] as const;
export type Action = (typeof ACTIONS)[number];

interface UseAbilityParams {
    user: User;
    check?: Action | null;
    checkFull?: string | null;
    hasAccess?: boolean;
}

export function useAbility({
    user,
    check = null,
    checkFull = null,
    hasAccess = true,
}: UseAbilityParams) {
    const routePermission = useMemo(() => {
        if (checkFull !== null) return checkFull;
        if (!check) return "";

        try {
            const routeName = route().current()?.toString() ?? "";
            const routeParts = routeName.split(".");

            switch (routeParts.length) {
                case 1:
                case 2:
                    return `${check}_${routeParts[0]}`;
                case 3:
                    return `${check}_${routeParts[1]}`;
                default:
                    return "";
            }
        } catch (error) {
            console.error("Error accessing route:", error);
            return "";
        }
    }, [check, checkFull]);

    const hasPermission = useMemo(() => {
        if (!hasAccess) return false;
        return (
            routePermission !== "" &&
            user.access_permissions?.includes(routePermission)
        );
    }, [hasAccess, routePermission, user.access_permissions]);

    return {
        hasPermission,
        routePermission,
    };
}
