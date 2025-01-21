"use client";

import { ArrowUpDown, MoreVertical } from "lucide-react";
import { ColumnDef } from "@tanstack/react-table";

import { Button } from "@/Components/ui/button";

import { User } from "@/types";
import { UserActions } from "./user-actions";

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

    {
        id: "actions",
        cell: ({ row }) => {
            const id = (String(row.original.id));

            return (
                <UserActions id={id}>
                    <Button variant="ghost" className="size-5 p-0">
                        <MoreVertical className="size-4 cursor-pointer" />
                    </Button>
                </UserActions>
            );
        },
    },
];
