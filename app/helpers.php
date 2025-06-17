<?php

/**
 * Theme helpers
 */

if (!function_exists('flag_url')) {
    function flag_url($countryCode) {
        if (empty($countryCode)) {
            return Vite::asset('resources/images/icons/flag.svg');
        }

        return 'https://purecatamphetamine.github.io/country-flag-icons/1x1/' . strtoupper($countryCode) . '.svg';
    }
}
