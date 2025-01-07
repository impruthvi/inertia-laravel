
import { Navigation } from "./Navigation";
import { DottedSeparator } from "./DottedSeparator";
import { Link } from "@inertiajs/react";

export const Sidebar = () => {
  return (
    <aside className="h-full bg-neutral-100 p-4 w-full">
      <Link href="/">
        <img src="/logo.svg" alt="Logo" width={164} height={48} />
      </Link>
      <DottedSeparator className="my-4" />
      <Navigation />
      <DottedSeparator className="my-4" />
    </aside>
  );
};
