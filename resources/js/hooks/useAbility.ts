import { useMemo } from "react";
import { User } from "@/types";

const PRIVILEGED_ROLES = [] as const;
type PrivilegedRole = (typeof PRIVILEGED_ROLES)[number];

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
    const isPrivilegedRole = useMemo(
        () => PRIVILEGED_ROLES.includes(user.role as PrivilegedRole),
        [user.role]
    );

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
        if (isPrivilegedRole) return true;
        return (
            routePermission !== "" &&
            user.access_permissions?.includes(routePermission)
        );
    }, [hasAccess, isPrivilegedRole, routePermission, user.access_permissions]);

    return {
        hasPermission,
        routePermission,
        isPrivilegedRole,
    };
}
