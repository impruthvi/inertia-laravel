"use client";

import { ArrowUpDown, MoreVertical } from "lucide-react";
import { ColumnDef } from "@tanstack/react-table";

import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";

import { User } from "@/types";

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: "name",
        header: ({ column }) => {
            return (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === "asc")
                    }
                >
                    Name
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
        cell: ({ row }) => {
            const name = row.original.name;
            return <p className="line-clamp-1">{name}</p>;
        },
    },
    {
        accessorKey: "email",
        header: ({ column }) => {
            return (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === "asc")
                    }
                >
                    Email
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
        cell: ({ row }) => {
            const email = row.original.email;
            return <p className="line-clamp-1">{email}</p>;
        },
    },

    // {
    //   id: "actions",
    //   cell: ({ row }) => {
    //     const id = row.original.$id;

    //     const projectId = row.original.projectId;

    //     return (
    //       <TaskActions id={id} projectId={projectId}>
    //         <Button className="size-8 p-0" variant="ghost">
    //           <MoreVertical className="size-4" />
    //         </Button>
    //       </TaskActions>
    //     );
    //   },
    // },
];
