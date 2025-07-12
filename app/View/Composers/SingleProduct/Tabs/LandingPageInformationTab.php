<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class LandingPageInformationTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Landing Page Information
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: LANDING PAGE INFORMATION =====
            [
                'key' => 'field_landing_page_info_tab',
                'label' => 'Landing Page Information',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_product_country_code',
                'label' => 'Country of Origin',
                'name' => 'landing_page_product_country_code',
                'type' => 'select',
                'choices' => get_country_choices(),
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select country...',
                'instructions' => 'Select the country where the landing page is published',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_rights_until_date',
                'label' => 'Rights Valid Until',
                'name' => 'landing_page_rights_until_date',
                'type' => 'date_picker',
                'display_format' => 'm/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
                'instructions' => 'Select the expiration date for landing page rights',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_product_target',
                'label' => 'Target',
                'name' => 'landing_page_product_target',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter target information...',
                'instructions' => 'Specify the target audience for this landing page',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_product_year',
                'label' => 'Year',
                'name' => 'landing_page_product_year',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => 4,
                'placeholder' => 'YYYY',
                'instructions' => 'Enter the year for this landing page',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_product_buyout',
                'label' => 'Buyout',
                'name' => 'landing_page_product_buyout',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter buyout information...',
                'instructions' => 'Specify the buyout information for this landing page',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_product_label',
                'label' => 'Product Label',
                'name' => 'landing_page_product_label',
                'type' => 'select',
                'choices' => [
                    'featured' => 'Featured',
                    'easy_adaptable' => 'Easy adaptable',
                ],
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select label...',
                'instructions' => 'Choose a label for this landing page',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_url',
                'label' => 'Landing Page URL',
                'name' => 'landing_page_url',
                'type' => 'url',
                'default_value' => '',
                'placeholder' => 'https://example.com/landing-page',
                'instructions' => 'Enter the URL of the landing page',
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
            [
                'key' => 'field_landing_page_description',
                'label' => 'Landing Page Description',
                'name' => 'landing_page_description',
                'type' => 'textarea',
                'default_value' => '',
                'rows' => 4,
                'placeholder' => 'Enter description of the landing page...',
                'instructions' => 'Brief description of the landing page content and purpose',
                'maxlength' => 500,
                'conditional_logic' => self::getConditionalLogicForProductType('landing_page'),
            ],
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['landing_page'])) {
            return null;
        }

        return [
            // Поля для Landing Page
            'landing_page_country_code' => self::getCountryCode($product),
            'landing_page_country_flag_url' => self::getCountryFlagUrl($product),
            'landing_page_rights_until_date' => self::getRightsUntilDate($product),
            'landing_page_rights_until_formatted' => self::getRightsUntilFormatted($product),
            'landing_page_target' => self::getTarget($product),
            'landing_page_year' => self::getYear($product),
            'landing_page_buyout' => self::getBuyout($product),
            'landing_page_label' => self::getLabel($product),
            'landing_page_url' => self::getLandingPageUrl($product),
            'landing_page_description' => self::getLandingPageDescription($product),
        ];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Get country code
     */
    private static function getCountryCode(WC_Product $product): string
    {
        return get_field('landing_page_product_country_code', $product->get_id()) ?: '';
    }

    /**
     * Get country flag URL
     */
    private static function getCountryFlagUrl(WC_Product $product): string
    {
        $country_code = self::getCountryCode($product);
        return flag_url($country_code);
    }

    /**
     * Get rights expiration date
     */
    private static function getRightsUntilDate(WC_Product $product): ?string
    {
        return get_field('landing_page_rights_until_date', $product->get_id());
    }

    /**
     * Get formatted rights expiration date
     */
    private static function getRightsUntilFormatted(WC_Product $product): string
    {
        $date = self::getRightsUntilDate($product);

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

    /**
     * Get target
     */
    private static function getTarget(WC_Product $product): string
    {
        return get_field('landing_page_product_target', $product->get_id()) ?: '';
    }

    /**
     * Get year
     */
    private static function getYear(WC_Product $product): string
    {
        return get_field('landing_page_product_year', $product->get_id()) ?: '';
    }

    /**
     * Get buyout
     */
    private static function getBuyout(WC_Product $product): string
    {
        return get_field('landing_page_product_buyout', $product->get_id()) ?: '';
    }

    /**
     * Get product label
     */
    private static function getLabel(WC_Product $product): string
    {
        return get_field('landing_page_product_label', $product->get_id()) ?: '';
    }

    /**
     * Get landing page URL
     */
    private static function getLandingPageUrl(WC_Product $product): string
    {
        return get_field('landing_page_url', $product->get_id()) ?: '';
    }

    /**
     * Get landing page description
     */
    private static function getLandingPageDescription(WC_Product $product): string
    {
        return get_field('landing_page_description', $product->get_id()) ?: '';
    }
}
