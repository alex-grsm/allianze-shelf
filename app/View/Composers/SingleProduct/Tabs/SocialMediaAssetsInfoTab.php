<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class SocialMediaAssetsInfoTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Social Media Assets Info
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: SOCIAL MEDIA ASSETS INFO =====
            [
                'key' => 'field_social_media_assets_info_tab',
                'label' => 'Social Media Assets Information',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_product_country_code',
                'label' => 'Country of Origin',
                'name' => 'sma_product_country_code',
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
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_rights_until_date',
                'label' => 'Rights Valid Until',
                'name' => 'sma_rights_until_date',
                'type' => 'date_picker',
                'display_format' => 'm/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
                'instructions' => 'Select the expiration date for product rights',
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_product_target',
                'label' => 'Target',
                'name' => 'sma_product_target',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter target information...',
                'instructions' => 'Specify the target information for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_product_year',
                'label' => 'Year',
                'name' => 'sma_product_year',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => 4,
                'placeholder' => 'YYYY',
                'instructions' => 'Enter the year for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_product_buyout',
                'label' => 'Buyout',
                'name' => 'sma_product_buyout',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter buyout information...',
                'instructions' => 'Specify the buyout information for this product',
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
            [
                'key' => 'field_sma_product_label',
                'label' => 'Product Label',
                'name' => 'sma_product_label',
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
                'conditional_logic' => self::getConditionalLogicForProductType('social_media_assets'),
            ],
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['social_media_assets'])) {
            return null;
        }

        return [
            // Поля для Social Media Assets Info
            'sma_country_code' => self::getCountryCode($product),
            'sma_country_flag_url' => self::getCountryFlagUrl($product),
            'sma_rights_until_date' => self::getRightsUntilDate($product),
            'sma_rights_until_formatted' => self::getRightsUntilFormatted($product),
            'sma_target' => self::getTarget($product),
            'sma_year' => self::getYear($product),
            'sma_buyout' => self::getBuyout($product),
            'sma_label' => self::getLabel($product),
        ];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Get country code
     */
    private static function getCountryCode(WC_Product $product): string
    {
        return get_field('sma_product_country_code', $product->get_id()) ?: '';
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
        return get_field('sma_rights_until_date', $product->get_id());
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
        return get_field('sma_product_target', $product->get_id()) ?: '';
    }

    /**
     * Get year
     */
    private static function getYear(WC_Product $product): string
    {
        return get_field('sma_product_year', $product->get_id()) ?: '';
    }

    /**
     * Get buyout
     */
    private static function getBuyout(WC_Product $product): string
    {
        return get_field('sma_product_buyout', $product->get_id()) ?: '';
    }

    /**
     * Get product label
     */
    private static function getLabel(WC_Product $product): string
    {
        return get_field('sma_product_label', $product->get_id()) ?: '';
    }
}
