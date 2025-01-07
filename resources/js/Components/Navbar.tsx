import { MobileSidebar } from "./MobileSidebar";

const pathnameMap = {
  // tasks: {
  //   title: "My Tasks",
  //   description: "View all of your tasks here",
  // },
  // projects: {
  //   title: "My Projects",
  //   description: "View all tasks of your projects here",
  // },
};

const defaultMap = {
  title: "Dashboard",
  description: "View your dashboard here",
};

export const Navbar = () => {
  const pathname = route().current() as string;

  const pathnameParts = pathname.split("/");
  const pathnameKey = pathnameParts[3] as keyof typeof pathnameMap;

  const { description, title } = pathnameMap[pathnameKey] || defaultMap;

  return (
    <nav className="pt-4 px-6 flex items-center justify-between">
      <div className="flex-col hidden lg:flex">
        <h1 className="text-2xl font-semibold">{title}</h1>
        <p className="text-muted-foreground">{description}</p>
      </div>
      <MobileSidebar />
    </nav>
  );
};
