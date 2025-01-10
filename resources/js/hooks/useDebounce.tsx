import { useEffect, useRef, useState } from "react";

/**
 * Custom hook that debounces a value by a specified delay.
 *
 * @param value - The value to be debounced.
 * @param delay - The debounce delay in milliseconds. Default is 500ms.
 * @param callback - Optional callback function to be executed after the debounce delay.
 * @returns The debounced value.
 *
 * @example
 * const debouncedSearchTerm = useDebounce(searchTerm, 300, (value) => {
 *   console.log("Debounced value:", value);
 * });
 */
export default function useDebounce(
    value: string,
    delay = 500,
    callback?: (value: string) => void,
) {
    const [debouncedValue, setDebouncedValue] = useState(value);
    const isInitialRender = useRef(true);

    useEffect(() => {
        // Skip callback execution on the initial render
        if (isInitialRender.current) {
            isInitialRender.current = false;
            return;
        }

        const id = setTimeout(() => {
            setDebouncedValue(value);

            if (callback && typeof callback === "function") {
                callback(value);
            }
        }, delay);

        return () => {
            clearTimeout(id);
        };
    }, [value, delay]);

    return debouncedValue;
}
