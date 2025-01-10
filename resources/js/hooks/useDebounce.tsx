import { useEffect, useState, useCallback } from "react";

/**
 * A custom hook that debounces a value and optionally triggers a callback.
 * @param value The value to debounce.
 * @param delay The debounce delay in milliseconds (default: 500ms).
 * @param callback Optional callback to invoke after the debounce delay.
 * @returns The debounced value.
 */
export default function useDebounce<T>(
    value: T,
    delay: number = 500,
    callback?: (value: T) => void
) {
    const [debouncedValue, setDebouncedValue] = useState<T>(value);

    const executeCallback = useCallback(() => {
        if (callback) {
            callback(value);
        }
        setDebouncedValue(value);
    }, [value, callback]);

    useEffect(() => {
        const id = setTimeout(executeCallback, delay);
        return () => clearTimeout(id);
    }, [value, delay, executeCallback]);

    return debouncedValue;
}
