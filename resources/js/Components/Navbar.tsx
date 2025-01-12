import { MobileSidebar } from "./MobileSidebar";
import { UserButton } from "./UserButton";

const pathnameMap = {
    users: {
        title: "Users",
        description: "View all users here",
        actions: {
            create: {
                title: "Create User",
                description: "Add a new user here",
            },
            edit: {
                title: "Edit User",
                description: "Modify user details here",
            },
        },
    },
    roles: {
        title: "Roles",
        description: "View all roles here",
        actions: {
            create: {
                title: "Create Role",
                description: "Add a new role here",
            },
            edit: {
                title: "Edit Role",
                description: "Modify role details here",
            },
        },
    },
};

const defaultMap = {
    title: "Dashboard",
    description: "View your dashboard here",
};

export const Navbar = () => {
    const pathname = route().current() as string;

    const pathnameParts = pathname.split(".");
    const mainKey = pathnameParts[1] as keyof typeof pathnameMap;
    const actionKey =
        pathnameParts[2] as keyof (typeof pathnameMap)["users"]["actions"];

    const defaultActionMap = { title: "", description: "" };
    const actionMap =
        pathnameMap[mainKey]?.actions?.[actionKey] || defaultActionMap;

    const title =
        actionMap.title || pathnameMap[mainKey]?.title || defaultMap.title;
    const description =
        actionMap.description ||
        pathnameMap[mainKey]?.description ||
        defaultMap.description;

    return (
        <nav className="pt-4 px-6 flex items-center justify-between">
            <div className="flex-col hidden lg:flex">
                <h1 className="text-2xl font-semibold">{title}</h1>
                <p className="text-muted-foreground">{description}</p>
            </div>
            <MobileSidebar />
            <UserButton />
        </nav>
    );
};
