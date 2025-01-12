import { ShieldIcon, UserIcon } from "lucide-react";
import { GoHome, GoHomeFill } from "react-icons/go";

interface Route {
    label: string;
    route: string;
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    activeIcon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    permission: string;
    matchRoutes?: string[]; // Additional routes to match for active state
}

export const routes: Route[] = [
    {
        label: "Dashboard",
        route: "admin.dashboard",
        icon: GoHome,
        activeIcon: GoHomeFill,
        permission: "access_users",
    },
    {
        label: "Users",
        route: "admin.users.index",
        icon: UserIcon,
        activeIcon: UserIcon,
        permission: "access_users",
        matchRoutes: ["admin.users.create", "admin.users.edit"], // Add specific sub-routes
    },
    {
        label: "Roles",
        route: "admin.roles.index",
        icon: ShieldIcon,
        activeIcon: ShieldIcon,
        permission: "access_roles",
        matchRoutes: ["admin.roles.create", "admin.roles.edit"], // Add specific sub-routes
    },
];
