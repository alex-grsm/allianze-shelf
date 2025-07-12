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

/**
 * Get complete list of countries with codes
 *
 * @return array Array of country codes and names
 */
function get_country_choices(): array
{
    return [
        'AT' => 'Austria (AT)',
        'AU' => 'Australia (AU)',
        'BG' => 'Bulgaria (BG)',
        'BR' => 'Brazil (BR)',
        'CH' => 'Switzerland (CH)',
        'CO' => 'Colombia (CO)',
        'CZ' => 'Czech Republic (CZ)',
        'DE' => 'Germany (DE)',
        'ES' => 'Spain (ES)',
        'FR' => 'France (FR)',
        'GB' => 'United Kingdom (GB)',
        'HR' => 'Croatia (HR)',
        'HU' => 'Hungary (HU)',
        'ID' => 'Indonesia (ID)',
        'IE' => 'Ireland (IE)',
        'IT' => 'Italy (IT)',
        'LK' => 'Sri Lanka (LK)',
        'MX' => 'Mexico (MX)',
        'MY' => 'Malaysia (MY)',
        'NL' => 'Netherlands (NL)',
        'PL' => 'Poland (PL)',
        'PT' => 'Portugal (PT)',
        'SG' => 'Singapore (SG)',
        'SI' => 'Slovenia (SI)',
        'SK' => 'Slovakia (SK)',
        'TH' => 'Thailand (TH)',
        'TR' => 'Turkey (TR)',
        'US' => 'United States (US)',
    ];
}
