
import { Navigation } from "./Navigation";
import { DottedSeparator } from "./DottedSeparator";
import { Link, usePage } from "@inertiajs/react";

export const Sidebar = () => {
  const authUser = usePage().props.auth.user;
  return (
    <aside className="h-full bg-neutral-100 p-4 w-full">
      <Link href="/">
        <img src="/logo.svg" alt="Logo" width={164} height={48} />
      </Link>
      <DottedSeparator className="my-4" />
      <Navigation user={authUser}/>
      <DottedSeparator className="my-4" />
    </aside>
  );
};
