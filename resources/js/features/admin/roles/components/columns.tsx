"use client";

import { ArrowUpDown, MoreVertical } from "lucide-react";
import { ColumnDef } from "@tanstack/react-table";

import { Button } from "@/Components/ui/button";

import { RoleActions } from "./role-actions";
import { Role } from "../types/role";

export const columns: ColumnDef<Role>[] = [
    {
        accessorKey: "display_name",
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
            const name = row.original.display_name;
            return <p className="line-clamp-1">{name}</p>;
        },
    },

    {
        id: "actions",
        cell: ({ row }) => {
            const id = row.original.id;

            return (
                <RoleActions id={id}>
                    <Button variant="ghost" className="size-5 p-0">
                        <MoreVertical className="size-4 cursor-pointer" />
                    </Button>
                </RoleActions>
            );
        },
    },
];
