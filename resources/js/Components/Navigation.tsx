import { cn, isRouteActive } from "@/lib/utils";
import { Link } from "@inertiajs/react";
import { HasAbility } from "@/Components/HasAbility"; // Adjust the import path as needed
import { User } from "@/types";
import { routes } from "@/lib/routes";

interface NavigationProps {
    user: User;
}

export const Navigation = ({ user }: NavigationProps) => {
    return (
        <nav aria-label="Main navigation">
            <ul className="flex flex-col space-y-1">
                {routes.map((item) => {
                    const isActive = isRouteActive(item.route);
                    const Icon = isActive ? item.activeIcon : item.icon;

                    return (
                        <HasAbility
                            key={item.route}
                            user={user}
                            checkFull={item.permission} // Pass the specific permission to check
                        >
                            <li>
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
                        </HasAbility>
                    );
                })}
            </ul>
        </nav>
    );
};

export default Navigation;
