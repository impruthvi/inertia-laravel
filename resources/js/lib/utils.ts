import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export const isRouteActive = (routeName: string) => {
  // Check if current route name starts with the given route name
  // This handles nested routes (e.g., tasks.index will match tasks.show)
  if (route().current(routeName + "*")) {
      return true;
  }

  // Exact match check
  return route().current(routeName);
};