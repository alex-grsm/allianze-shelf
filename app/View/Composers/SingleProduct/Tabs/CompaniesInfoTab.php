<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class CompaniesInfoTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Companies Info
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: COMPANIES =====
            [
                'key' => 'field_companies_info_tab',
                'label' => 'Companies Information',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
            [
                'key' => 'field_product_country_code',
                'label' => 'Country of Origin',
                'name' => 'product_country_code',
                'type' => 'select',
                'choices' => get_country_choices(),
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select country...',
                'instructions' => 'Select the country where the product is manufactured',
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
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
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
            [
                'key' => 'field_product_target',
                'label' => 'Target',
                'name' => 'product_target',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter target information...',
                'instructions' => 'Specify the target information for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
            [
                'key' => 'field_product_year',
                'label' => 'Year',
                'name' => 'product_year',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => 4,
                'placeholder' => 'YYYY',
                'instructions' => 'Enter the year for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
            [
                'key' => 'field_product_buyout',
                'label' => 'Buyout',
                'name' => 'product_buyout',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter buyout information...',
                'instructions' => 'Specify the buyout information for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
            [
                'key' => 'field_product_label',
                'label' => 'Product Label',
                'name' => 'product_label',
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
                'instructions' => 'Choose a label for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies'])) {
            return null;
        }

        return [
            // Поля для Companies
            'country_code' => self::getCountryCode($product),
            'country_flag_url' => self::getCountryFlagUrl($product),
            'rights_until_date' => self::getRightsUntilDate($product),
            'rights_until_formatted' => self::getRightsUntilFormatted($product),
            'target' => self::getTarget($product),
            'year' => self::getYear($product),
            'buyout' => self::getBuyout($product),
            'label' => self::getLabel($product),
        ];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Get country code
     */
    private static function getCountryCode(WC_Product $product): string
    {
        return get_field('product_country_code', $product->get_id()) ?: '';
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
        return get_field('rights_until_date', $product->get_id());
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
        return get_field('product_target', $product->get_id()) ?: '';
    }

    /**
     * Get year
     */
    private static function getYear(WC_Product $product): string
    {
        return get_field('product_year', $product->get_id()) ?: '';
    }

    /**
     * Get buyout
     */
    private static function getBuyout(WC_Product $product): string
    {
        return get_field('product_buyout', $product->get_id()) ?: '';
    }

    /**
     * Get product label
     */
    private static function getLabel(WC_Product $product): string
    {
        return get_field('product_label', $product->get_id()) ?: '';
    }
}
