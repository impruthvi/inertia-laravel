import { useEffect, useState } from "react";

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
