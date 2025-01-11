import { UserIcon } from "lucide-react";
import { GoHome, GoHomeFill } from "react-icons/go";

interface Route {
    label: string;
    route: string;
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    activeIcon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    permission: string;
}

export const routes: Route[] = [
    {
        label: "Dashboard",
        route: "admin.dashboard",
        icon: GoHome,
        activeIcon: GoHomeFill,
        permission: "access_users", // for the now just added user access permission
    },
    {
        label: "Users",
        route: "admin.users.index",
        icon: UserIcon,
        activeIcon: UserIcon,
        permission: "access_users",
    },
];
