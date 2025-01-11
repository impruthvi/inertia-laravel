import { PropsWithChildren } from "react";
import { User } from "@/types";
import { Action, useAbility } from "@/hooks/useAbility";

interface HasAbilityProps {
    user: User;
    check?: Action | null;
    checkFull?: string | null;
    hasAccess?: boolean;
}

export function HasAbility({
    user,
    check = null,
    checkFull = null,
    hasAccess = true,
    children,
}: PropsWithChildren<HasAbilityProps>) {
    const { hasPermission } = useAbility({ user, check, checkFull, hasAccess });
    return hasPermission ? <>{children}</> : null;
}
