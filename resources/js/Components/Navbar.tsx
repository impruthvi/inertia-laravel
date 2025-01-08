import { MobileSidebar } from "./MobileSidebar";
import { UserButton } from "./UserButton";

const pathnameMap = {
    users: {
        title: "Users",
        description: "View all users here",
    },
};

const defaultMap = {
    title: "Dashboard",
    description: "View your dashboard here",
};

export const Navbar = () => {
    const pathname = route().current() as string;

    const pathnameParts = pathname.split(".");
    const pathnameKey = pathnameParts[1] as keyof typeof pathnameMap;

    const { description, title } = pathnameMap[pathnameKey] || defaultMap;

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
