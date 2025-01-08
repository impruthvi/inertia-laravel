import { PaginationItem } from "@/types";
import { Button } from "./ui/button";
import { MoreHorizontal, ChevronsLeft, ChevronsRight } from "lucide-react";
import { router } from "@inertiajs/react";

interface PaginationProps {
    links: PaginationItem[];
}

const Pagination = ({ links }: PaginationProps) => {
    if (!links || links.length === 0) return null;

    const handlePageChange = (url: string | null) => {
        if (url) {
            router.visit(url, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            });
        }
    };

    // Find previous and next links
    const prevLink = links.find((link) => link.label.includes("Previous"));
    const nextLink = links.find((link) => link.label.includes("Next"));

    // Get numbered pages (exclude prev/next links)
    const numberLinks = links.filter(
        (link) =>
            !link.label.includes("Previous") && !link.label.includes("Next")
    );

    const renderPageNumbers = () => {
        const totalPages = numberLinks.length;
        const visiblePages = [];
        const currentPage = numberLinks.findIndex((link) => link.active) + 1;

        // Always show first page
        visiblePages.push(numberLinks[0]);

        // Logic for middle pages
        for (let i = 0; i < totalPages; i++) {
            const pageNum = i + 1;

            // Show current page and one page before and after
            if (
                pageNum === currentPage - 1 ||
                pageNum === currentPage ||
                pageNum === currentPage + 1
            ) {
                visiblePages.push(numberLinks[i]);
            }
            // Add ellipsis after first page if there's a gap
            else if (pageNum === 2 && currentPage > 4) {
                visiblePages.push("ellipsis1");
            }
            // Add ellipsis before last page if there's a gap
            else if (
                pageNum === totalPages - 1 &&
                currentPage < totalPages - 3
            ) {
                visiblePages.push("ellipsis2");
            }
        }

        // Always show last page if we have more than one page
        if (totalPages > 1) {
            visiblePages.push(numberLinks[totalPages - 1]);
        }

        // Remove duplicates and nulls
        return visiblePages.filter(
            (item, index, self) => item && self.indexOf(item) === index
        );
    };

    const firstPage = numberLinks[0];
    const lastPage = numberLinks[numberLinks.length - 1];

    return (
        <div className="flex items-center justify-end space-x-2 py-4">
            {/* First page button */}
            <Button
                variant="outline"
                size="sm"
                onClick={() =>
                    firstPage?.url && handlePageChange(firstPage.url)
                }
                disabled={firstPage?.active}
                className="select-none"
            >
                <ChevronsLeft className="h-4 w-4" />
            </Button>

            {/* Previous button */}
            <Button
                variant="outline"
                size="sm"
                onClick={() => prevLink?.url && handlePageChange(prevLink.url)}
                disabled={!prevLink?.url}
                className="select-none"
            >
                Previous
            </Button>

            {/* Numbered pages with ellipsis */}
            <div className="flex items-center space-x-1">
                {renderPageNumbers().map((item, index) => {
                    if (item === "ellipsis1" || item === "ellipsis2") {
                        return (
                            <div
                                key={`ellipsis-${item}`}
                                className="w-8 flex justify-center items-center"
                            >
                                <MoreHorizontal className="h-4 w-4" />
                            </div>
                        );
                    }

                    const link = item as PaginationItem;
                    return (
                        <Button
                            key={index}
                            variant={link.active ? "primary" : "outline"}
                            size="sm"
                            onClick={() =>
                                link.url && handlePageChange(link.url)
                            }
                            disabled={link.active}
                            className="min-w-[32px] select-none"
                        >
                            {link.label}
                        </Button>
                    );
                })}
            </div>

            {/* Next button */}
            <Button
                variant="outline"
                size="sm"
                onClick={() => nextLink?.url && handlePageChange(nextLink.url)}
                disabled={!nextLink?.url}
                className="select-none"
            >
                Next
            </Button>

            {/* Last page button */}
            <Button
                variant="outline"
                size="sm"
                onClick={() => lastPage?.url && handlePageChange(lastPage.url)}
                disabled={lastPage?.active}
                className="select-none"
            >
                <ChevronsRight className="h-4 w-4" />
            </Button>
        </div>
    );
};

export default Pagination;
