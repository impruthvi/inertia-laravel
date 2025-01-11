<?php

if (!function_exists('generatePassword')) {
    function generatePassword(string $string = ''): string
    {
        return \Hash::make($string);
    }
}

if (! function_exists('get_ability')) {
    function get_ability($access): string
    {
        $routeNameArray = explode('.', request()->route()->getName());
        switch (count($routeNameArray)) {
            case '1':
            case '2':
                return $access . '_' . $routeNameArray[0];

            case '3':
                return $access . '_' . $routeNameArray[1];
        }
    }
}
