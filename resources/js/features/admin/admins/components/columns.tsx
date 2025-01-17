"use client";

import { ArrowUpDown, MoreVertical } from "lucide-react";
import { ColumnDef } from "@tanstack/react-table";

import { Button } from "@/Components/ui/button";

import { AdminActions } from "./admin-actions";
import { Admin } from "../types/admin";

export const columns: ColumnDef<Admin>[] = [
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
            const id = row.original.id;

            return (
                <AdminActions id={id}>
                    <Button variant="ghost" className="size-5 p-0">
                        <MoreVertical className="size-4 cursor-pointer" />
                    </Button>
                </AdminActions>
            );
        },
    },
];
