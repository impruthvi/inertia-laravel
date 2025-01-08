<?php

if (!function_exists('generatePassword')) {
    function generatePassword(string $string = ''): string
    {
        return \Hash::make($string);
    }
}