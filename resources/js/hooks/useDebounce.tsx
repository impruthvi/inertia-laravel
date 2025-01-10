import { useEffect, useState } from "react";

/**
 * Custom hook that debounces a value by a specified delay.
 *
 * @param value - The value to be debounced.
 * @param delay - The debounce delay in milliseconds. Default is 500ms.
 * @param callback - Optional callback function to be executed after the debounce delay.
 * @returns The debounced value.
 *
 * @example
 * const debouncedSearchTerm = useDebounce(searchTerm, 300);
 */
export default function useDebounce(
    value: string,
    delay = 500,
    callback?: (value: string) => void,
) {
    const [debouncedValue, setDebouncedValue] = useState(value);

    useEffect(() => {
        const id = setTimeout(() => {
            if (callback && typeof callback === "function") {
                callback(value);
            }

            setDebouncedValue(value);
        }, delay);

        return () => {
            clearTimeout(id);
        };
    }, [value, delay]);

    return debouncedValue;
}
