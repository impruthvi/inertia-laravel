import { cn, isRouteActive } from "@/lib/utils";
import { Link } from "@inertiajs/react";
import { GoHome, GoHomeFill } from "react-icons/go";

const routes = [
    {
        label: "Dashboard",
        route: "admin.dashboard",
        icon: GoHome,
        activeIcon: GoHomeFill,
    },
];

export const Navigation = () => {
    return (
        <nav aria-label="Main navigation">
            <ul className="flex flex-col space-y-1">
                {routes.map((item) => {
                    const isActive = isRouteActive(item.route);
                    const Icon = isActive ? item.activeIcon : item.icon;

                    return (
                        <li key={item.route}>
                            <Link
                                href={route(item.route)}
                                className="block"
                                aria-current={isActive ? "page" : undefined}
                            >
                                <div
                                    className={cn(
                                        "flex items-center gap-2.5 p-2.5 rounded-md",
                                        "font-medium transition-colors duration-200",
                                        "hover:bg-white hover:text-primary hover:shadow-sm",
                                        "text-neutral-500",
                                        isActive && [
                                            "bg-white",
                                            "shadow-sm",
                                            "text-primary",
                                            "hover:bg-white/90",
                                        ]
                                    )}
                                >
                                    <Icon
                                        className={cn(
                                            "w-5 h-5",
                                            isActive
                                                ? "text-primary"
                                                : "text-neutral-500"
                                        )}
                                        aria-hidden="true"
                                    />
                                    <span>{item.label}</span>
                                </div>
                            </Link>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
};

export default Navigation;
