<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class ProductAcfFields extends Composer
{
    protected static $views = [
        'partials.single-product.product-summary',
    ];

    /**
     * Register composer and ACF fields
     */
    public static function register(): void
    {
        // Register ACF fields
        static::registerAcfFields();

        // Composer will be registered automatically through Sage
    }

    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['productAcfFields' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['productAcfFields' => null];
        }

        return [
            'productAcfFields' => [
                'country_code' => $this->getCountryCode($product),
                'country_flag_url' => $this->getCountryFlagUrl($product),
                'rights_until_date' => $this->getRightsUntilDate($product),
                'rights_until_formatted' => $this->getRightsUntilFormatted($product),
            ]
        ];
    }

    /**
     * Register ACF fields programmatically
     */
    private static function registerAcfFields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        add_action('acf/init', function() {
            acf_add_local_field_group([
                'key' => 'group_product_additional_info',
                'title' => 'Product Additional Information',
                'fields' => [
                    [
                        'key' => 'field_product_country_code',
                        'label' => 'Country of Origin',
                        'name' => 'product_country_code',
                        'type' => 'select',
                        'choices' => static::getCountryChoices(),
                        'default_value' => '',
                        'allow_null' => 1,
                        'multiple' => 0,
                        'ui' => 1,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => 'Select country...',
                        'instructions' => 'Select the country where the product is manufactured',
                    ],
                    [
                        'key' => 'field_rights_until_date',
                        'label' => 'Rights Valid Until',
                        'name' => 'rights_until_date',
                        'type' => 'date_picker',
                        'display_format' => 'm/Y',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                        'instructions' => 'Select the expiration date for product rights',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'product',
                        ],
                    ],
                ],
                'menu_order' => 20,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
            ]);
        });
    }

    /**
     * Get complete list of countries
     */
    private static function getCountryChoices(): array
    {
        return [
            'AD' => 'Andorra (AD)',
            'AE' => 'United Arab Emirates (AE)',
            'AF' => 'Afghanistan (AF)',
            'AG' => 'Antigua and Barbuda (AG)',
            'AI' => 'Anguilla (AI)',
            'AL' => 'Albania (AL)',
            'AM' => 'Armenia (AM)',
            'AO' => 'Angola (AO)',
            'AQ' => 'Antarctica (AQ)',
            'AR' => 'Argentina (AR)',
            'AS' => 'American Samoa (AS)',
            'AT' => 'Austria (AT)',
            'AU' => 'Australia (AU)',
            'AW' => 'Aruba (AW)',
            'AX' => 'Åland Islands (AX)',
            'AZ' => 'Azerbaijan (AZ)',
            'BA' => 'Bosnia and Herzegovina (BA)',
            'BB' => 'Barbados (BB)',
            'BD' => 'Bangladesh (BD)',
            'BE' => 'Belgium (BE)',
            'BF' => 'Burkina Faso (BF)',
            'BG' => 'Bulgaria (BG)',
            'BH' => 'Bahrain (BH)',
            'BI' => 'Burundi (BI)',
            'BJ' => 'Benin (BJ)',
            'BL' => 'Saint Barthélemy (BL)',
            'BM' => 'Bermuda (BM)',
            'BN' => 'Brunei (BN)',
            'BO' => 'Bolivia (BO)',
            'BQ' => 'Caribbean Netherlands (BQ)',
            'BR' => 'Brazil (BR)',
            'BS' => 'Bahamas (BS)',
            'BT' => 'Bhutan (BT)',
            'BV' => 'Bouvet Island (BV)',
            'BW' => 'Botswana (BW)',
            'BY' => 'Belarus (BY)',
            'BZ' => 'Belize (BZ)',
            'CA' => 'Canada (CA)',
            'CC' => 'Cocos Islands (CC)',
            'CD' => 'Congo (Kinshasa) (CD)',
            'CF' => 'Central African Republic (CF)',
            'CG' => 'Congo (Brazzaville) (CG)',
            'CH' => 'Switzerland (CH)',
            'CI' => 'Côte d\'Ivoire (CI)',
            'CK' => 'Cook Islands (CK)',
            'CL' => 'Chile (CL)',
            'CM' => 'Cameroon (CM)',
            'CN' => 'China (CN)',
            'CO' => 'Colombia (CO)',
            'CR' => 'Costa Rica (CR)',
            'CU' => 'Cuba (CU)',
            'CV' => 'Cape Verde (CV)',
            'CW' => 'Curaçao (CW)',
            'CX' => 'Christmas Island (CX)',
            'CY' => 'Cyprus (CY)',
            'CZ' => 'Czech Republic (CZ)',
            'DE' => 'Germany (DE)',
            'DJ' => 'Djibouti (DJ)',
            'DK' => 'Denmark (DK)',
            'DM' => 'Dominica (DM)',
            'DO' => 'Dominican Republic (DO)',
            'DZ' => 'Algeria (DZ)',
            'EC' => 'Ecuador (EC)',
            'EE' => 'Estonia (EE)',
            'EG' => 'Egypt (EG)',
            'EH' => 'Western Sahara (EH)',
            'ER' => 'Eritrea (ER)',
            'ES' => 'Spain (ES)',
            'ET' => 'Ethiopia (ET)',
            'FI' => 'Finland (FI)',
            'FJ' => 'Fiji (FJ)',
            'FK' => 'Falkland Islands (FK)',
            'FM' => 'Micronesia (FM)',
            'FO' => 'Faroe Islands (FO)',
            'FR' => 'France (FR)',
            'GA' => 'Gabon (GA)',
            'GB' => 'United Kingdom (GB)',
            'GD' => 'Grenada (GD)',
            'GE' => 'Georgia (GE)',
            'GF' => 'French Guiana (GF)',
            'GG' => 'Guernsey (GG)',
            'GH' => 'Ghana (GH)',
            'GI' => 'Gibraltar (GI)',
            'GL' => 'Greenland (GL)',
            'GM' => 'Gambia (GM)',
            'GN' => 'Guinea (GN)',
            'GP' => 'Guadeloupe (GP)',
            'GQ' => 'Equatorial Guinea (GQ)',
            'GR' => 'Greece (GR)',
            'GS' => 'South Georgia (GS)',
            'GT' => 'Guatemala (GT)',
            'GU' => 'Guam (GU)',
            'GW' => 'Guinea-Bissau (GW)',
            'GY' => 'Guyana (GY)',
            'HK' => 'Hong Kong (HK)',
            'HM' => 'Heard Island (HM)',
            'HN' => 'Honduras (HN)',
            'HR' => 'Croatia (HR)',
            'HT' => 'Haiti (HT)',
            'HU' => 'Hungary (HU)',
            'ID' => 'Indonesia (ID)',
            'IE' => 'Ireland (IE)',
            'IL' => 'Israel (IL)',
            'IM' => 'Isle of Man (IM)',
            'IN' => 'India (IN)',
            'IO' => 'British Indian Ocean Territory (IO)',
            'IQ' => 'Iraq (IQ)',
            'IR' => 'Iran (IR)',
            'IS' => 'Iceland (IS)',
            'IT' => 'Italy (IT)',
            'JE' => 'Jersey (JE)',
            'JM' => 'Jamaica (JM)',
            'JO' => 'Jordan (JO)',
            'JP' => 'Japan (JP)',
            'KE' => 'Kenya (KE)',
            'KG' => 'Kyrgyzstan (KG)',
            'KH' => 'Cambodia (KH)',
            'KI' => 'Kiribati (KI)',
            'KM' => 'Comoros (KM)',
            'KN' => 'Saint Kitts and Nevis (KN)',
            'KP' => 'North Korea (KP)',
            'KR' => 'South Korea (KR)',
            'KW' => 'Kuwait (KW)',
            'KY' => 'Cayman Islands (KY)',
            'KZ' => 'Kazakhstan (KZ)',
            'LA' => 'Laos (LA)',
            'LB' => 'Lebanon (LB)',
            'LC' => 'Saint Lucia (LC)',
            'LI' => 'Liechtenstein (LI)',
            'LK' => 'Sri Lanka (LK)',
            'LR' => 'Liberia (LR)',
            'LS' => 'Lesotho (LS)',
            'LT' => 'Lithuania (LT)',
            'LU' => 'Luxembourg (LU)',
            'LV' => 'Latvia (LV)',
            'LY' => 'Libya (LY)',
            'MA' => 'Morocco (MA)',
            'MC' => 'Monaco (MC)',
            'MD' => 'Moldova (MD)',
            'ME' => 'Montenegro (ME)',
            'MF' => 'Saint Martin (MF)',
            'MG' => 'Madagascar (MG)',
            'MH' => 'Marshall Islands (MH)',
            'MK' => 'North Macedonia (MK)',
            'ML' => 'Mali (ML)',
            'MM' => 'Myanmar (MM)',
            'MN' => 'Mongolia (MN)',
            'MO' => 'Macao (MO)',
            'MP' => 'Northern Mariana Islands (MP)',
            'MQ' => 'Martinique (MQ)',
            'MR' => 'Mauritania (MR)',
            'MS' => 'Montserrat (MS)',
            'MT' => 'Malta (MT)',
            'MU' => 'Mauritius (MU)',
            'MV' => 'Maldives (MV)',
            'MW' => 'Malawi (MW)',
            'MX' => 'Mexico (MX)',
            'MY' => 'Malaysia (MY)',
            'MZ' => 'Mozambique (MZ)',
            'NA' => 'Namibia (NA)',
            'NC' => 'New Caledonia (NC)',
            'NE' => 'Niger (NE)',
            'NF' => 'Norfolk Island (NF)',
            'NG' => 'Nigeria (NG)',
            'NI' => 'Nicaragua (NI)',
            'NL' => 'Netherlands (NL)',
            'NO' => 'Norway (NO)',
            'NP' => 'Nepal (NP)',
            'NR' => 'Nauru (NR)',
            'NU' => 'Niue (NU)',
            'NZ' => 'New Zealand (NZ)',
            'OM' => 'Oman (OM)',
            'PA' => 'Panama (PA)',
            'PE' => 'Peru (PE)',
            'PF' => 'French Polynesia (PF)',
            'PG' => 'Papua New Guinea (PG)',
            'PH' => 'Philippines (PH)',
            'PK' => 'Pakistan (PK)',
            'PL' => 'Poland (PL)',
            'PM' => 'Saint Pierre and Miquelon (PM)',
            'PN' => 'Pitcairn (PN)',
            'PR' => 'Puerto Rico (PR)',
            'PS' => 'Palestine (PS)',
            'PT' => 'Portugal (PT)',
            'PW' => 'Palau (PW)',
            'PY' => 'Paraguay (PY)',
            'QA' => 'Qatar (QA)',
            'RE' => 'Réunion (RE)',
            'RO' => 'Romania (RO)',
            'RS' => 'Serbia (RS)',
            'RU' => 'Russia (RU)',
            'RW' => 'Rwanda (RW)',
            'SA' => 'Saudi Arabia (SA)',
            'SB' => 'Solomon Islands (SB)',
            'SC' => 'Seychelles (SC)',
            'SD' => 'Sudan (SD)',
            'SE' => 'Sweden (SE)',
            'SG' => 'Singapore (SG)',
            'SH' => 'Saint Helena (SH)',
            'SI' => 'Slovenia (SI)',
            'SJ' => 'Svalbard and Jan Mayen (SJ)',
            'SK' => 'Slovakia (SK)',
            'SL' => 'Sierra Leone (SL)',
            'SM' => 'San Marino (SM)',
            'SN' => 'Senegal (SN)',
            'SO' => 'Somalia (SO)',
            'SR' => 'Suriname (SR)',
            'SS' => 'South Sudan (SS)',
            'ST' => 'São Tomé and Príncipe (ST)',
            'SV' => 'El Salvador (SV)',
            'SX' => 'Sint Maarten (SX)',
            'SY' => 'Syria (SY)',
            'SZ' => 'Eswatini (SZ)',
            'TC' => 'Turks and Caicos Islands (TC)',
            'TD' => 'Chad (TD)',
            'TF' => 'French Southern Territories (TF)',
            'TG' => 'Togo (TG)',
            'TH' => 'Thailand (TH)',
            'TJ' => 'Tajikistan (TJ)',
            'TK' => 'Tokelau (TK)',
            'TL' => 'Timor-Leste (TL)',
            'TM' => 'Turkmenistan (TM)',
            'TN' => 'Tunisia (TN)',
            'TO' => 'Tonga (TO)',
            'TR' => 'Turkey (TR)',
            'TT' => 'Trinidad and Tobago (TT)',
            'TV' => 'Tuvalu (TV)',
            'TW' => 'Taiwan (TW)',
            'TZ' => 'Tanzania (TZ)',
            'UA' => 'Ukraine (UA)',
            'UG' => 'Uganda (UG)',
            'UM' => 'U.S. Outlying Islands (UM)',
            'US' => 'United States (US)',
            'UY' => 'Uruguay (UY)',
            'UZ' => 'Uzbekistan (UZ)',
            'VA' => 'Vatican City (VA)',
            'VC' => 'Saint Vincent and the Grenadines (VC)',
            'VE' => 'Venezuela (VE)',
            'VG' => 'British Virgin Islands (VG)',
            'VI' => 'U.S. Virgin Islands (VI)',
            'VN' => 'Vietnam (VN)',
            'VU' => 'Vanuatu (VU)',
            'WF' => 'Wallis and Futuna (WF)',
            'WS' => 'Samoa (WS)',
            'YE' => 'Yemen (YE)',
            'YT' => 'Mayotte (YT)',
            'ZA' => 'South Africa (ZA)',
            'ZM' => 'Zambia (ZM)',
            'ZW' => 'Zimbabwe (ZW)',
        ];
    }

    /**
     * Get country code
     */
    private function getCountryCode(WC_Product $product): string
    {
        return get_field('product_country_code', $product->get_id()) ?: ''; // Возвращаем пустую строку вместо 'DE'
    }

    /**
     * Get country flag URL
     */
    private function getCountryFlagUrl(WC_Product $product): string
    {
        $country_code = $this->getCountryCode($product);
        return flag_url($country_code); // Если пустая строка, то сработает заглушка
    }

    /**
     * Get rights expiration date
     */
    private function getRightsUntilDate(WC_Product $product): ?string
    {
        return get_field('rights_until_date', $product->get_id());
    }

    /**
     * Get formatted rights expiration date
     */
    private function getRightsUntilFormatted(WC_Product $product): string
    {
        $date = $this->getRightsUntilDate($product);

        if (!$date) {
            return '';
        }

        // If it's a DateTime object
        if ($date instanceof \DateTime) {
            return $date->format('m/Y');
        }

        // If it's a string
        if (is_string($date)) {
            $timestamp = strtotime($date);
            return $timestamp ? date('m/Y', $timestamp) : '';
        }

        return '';
    }
}
