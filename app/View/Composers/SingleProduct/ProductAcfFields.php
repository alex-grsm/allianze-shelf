<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class ProductAcfFields extends Composer
{
    protected static $views = [
        'partials.single-product.product-summary',
        'partials.product-card',
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
        // Проверяем WooCommerce
        if (!function_exists('wc_get_product')) {
            return ['productAcfFields' => null];
        }

        global $product;

        // Если это не страница товара, пытаемся получить товар из текущего поста в цикле
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        // Если всё ещё нет товара, возвращаем null
        if (!$product instanceof WC_Product) {
            return ['productAcfFields' => null];
        }

        return [
            'productAcfFields' => [
                'country_code' => $this->getCountryCode($product),
                'country_flag_url' => $this->getCountryFlagUrl($product),
                'rights_until_date' => $this->getRightsUntilDate($product),
                'rights_until_formatted' => $this->getRightsUntilFormatted($product),
                'target' => $this->getTarget($product),
                'year' => $this->getYear($product),
                'buyout' => $this->getBuyout($product),
                'label' => $this->getLabel($product),
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
                        'choices' => get_country_choices(),
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
                    [
                        'key' => 'field_product_target',
                        'label' => 'Target',
                        'name' => 'product_target',
                        'type' => 'text',
                        'default_value' => '',
                        'maxlength' => '',
                        'placeholder' => 'Enter target information...',
                        'instructions' => 'Specify the target information for this product',
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
     * Get country code
     */
    private function getCountryCode(WC_Product $product): string
    {
        return get_field('product_country_code', $product->get_id()) ?: '';
    }

    /**
     * Get country flag URL
     */
    private function getCountryFlagUrl(WC_Product $product): string
    {
        $country_code = $this->getCountryCode($product);
        return flag_url($country_code);
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

    /**
     * Get target
     */
    private function getTarget(WC_Product $product): string
    {
        return get_field('product_target', $product->get_id()) ?: '';
    }

    /**
     * Get year
     */
    private function getYear(WC_Product $product): string
    {
        return get_field('product_year', $product->get_id()) ?: '';
    }

    /**
     * Get buyout
     */
    private function getBuyout(WC_Product $product): string
    {
        return get_field('product_buyout', $product->get_id()) ?: '';
    }

    /**
     * Get product label
     */
    private function getLabel(WC_Product $product): string
    {
        return get_field('product_label', $product->get_id()) ?: '';
    }
}
