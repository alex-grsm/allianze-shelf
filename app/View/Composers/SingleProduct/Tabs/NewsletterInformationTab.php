<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class NewsletterInformationTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Newsletter Information
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: NEWSLETTER INFORMATION =====
            [
                'key' => 'field_newsletter_info_tab',
                'label' => 'Newsletter Information',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_product_country_code',
                'label' => 'Country of Origin',
                'name' => 'newsletter_product_country_code',
                'type' => 'select',
                'choices' => get_country_choices(),
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select country...',
                'instructions' => 'Select the country where the newsletter is published',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_rights_until_date',
                'label' => 'Rights Valid Until',
                'name' => 'newsletter_rights_until_date',
                'type' => 'date_picker',
                'display_format' => 'm/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
                'instructions' => 'Select the expiration date for newsletter rights',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_product_target',
                'label' => 'Target',
                'name' => 'newsletter_product_target',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter target information...',
                'instructions' => 'Specify the target audience for this newsletter',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_product_year',
                'label' => 'Year',
                'name' => 'newsletter_product_year',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => 4,
                'placeholder' => 'YYYY',
                'instructions' => 'Enter the year for this newsletter',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_product_buyout',
                'label' => 'Buyout',
                'name' => 'newsletter_product_buyout',
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter buyout information...',
                'instructions' => 'Specify the buyout information for this newsletter',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
            [
                'key' => 'field_newsletter_product_label',
                'label' => 'Product Label',
                'name' => 'newsletter_product_label',
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
                'instructions' => 'Choose a label for this newsletter',
                'conditional_logic' => self::getConditionalLogicForProductType('newsletter'),
            ],
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['newsletter'])) {
            return null;
        }

        return [
            // Поля для Newsletter
            'newsletter_country_code' => self::getCountryCode($product),
            'newsletter_country_flag_url' => self::getCountryFlagUrl($product),
            'newsletter_rights_until_date' => self::getRightsUntilDate($product),
            'newsletter_rights_until_formatted' => self::getRightsUntilFormatted($product),
            'newsletter_target' => self::getTarget($product),
            'newsletter_year' => self::getYear($product),
            'newsletter_buyout' => self::getBuyout($product),
            'newsletter_label' => self::getLabel($product),
        ];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Get country code
     */
    private static function getCountryCode(WC_Product $product): string
    {
        return get_field('newsletter_product_country_code', $product->get_id()) ?: '';
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
        return get_field('newsletter_rights_until_date', $product->get_id());
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
        return get_field('newsletter_product_target', $product->get_id()) ?: '';
    }

    /**
     * Get year
     */
    private static function getYear(WC_Product $product): string
    {
        return get_field('newsletter_product_year', $product->get_id()) ?: '';
    }

    /**
     * Get buyout
     */
    private static function getBuyout(WC_Product $product): string
    {
        return get_field('newsletter_product_buyout', $product->get_id()) ?: '';
    }

    /**
     * Get product label
     */
    private static function getLabel(WC_Product $product): string
    {
        return get_field('newsletter_product_label', $product->get_id()) ?: '';
    }
}
