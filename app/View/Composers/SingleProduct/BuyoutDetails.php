<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class BuyoutDetails extends Composer
{
    protected static $views = [
        'partials.single-product.buyout-details',
    ];

    /**
     * Register ACF fields and hooks
     */
    public static function register()
    {
        add_action('acf/init', [self::class, 'registerFields']);
    }

    /**
     * Data for view
     */
    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['buyoutDetails' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['buyoutDetails' => null];
        }

        return [
            'buyoutDetails' => $this->getBuyoutData($product)
        ];
    }

    /**
     * Register ACF fields for buyout details
     */
    public static function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_buyout_details',
            'title' => 'Buyout Details',
            'fields' => [
                [
                    'key' => 'field_buyout_description',
                    'label' => 'Buyout Description',
                    'name' => 'buyout_description',
                    'type' => 'textarea',
                    'instructions' => 'Enter detailed description for buyout terms and conditions.',
                    'required' => 0,
                    'rows' => 4,
                    'placeholder' => 'Enter buyout details description...',
                    'maxlength' => 500,
                ],
                [
                    'key' => 'field_buyout_table_image',
                    'label' => 'Buyout Table (PNG)',
                    'name' => 'buyout_table_image',
                    'type' => 'image',
                    'instructions' => 'Upload PNG table with buyout details.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'mime_types' => 'png,jpg,jpeg',
                ],
                [
                    'key' => 'field_buyout_enabled',
                    'label' => 'Enable Buyout Details',
                    'name' => 'buyout_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Enable to show buyout details section on product page.',
                    'required' => 0,
                    'message' => 'Show buyout details on product page',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Yes',
                    'ui_off_text' => 'No',
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
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'description' => 'Configure buyout details and pricing tables for product',
        ]);
    }

    /**
     * Get buyout data for product (for view)
     */
    private function getBuyoutData(WC_Product $product): ?array
    {
        $product_id = $product->get_id();

        // Check if buyout is enabled
        if (!self::isBuyoutEnabled($product_id)) {
            return null;
        }

        $description = get_field('buyout_description', $product_id);
        $table_image = get_field('buyout_table_image', $product_id);
        $table_alt = get_field('buyout_table_alt', $product_id);

        // If no content, return null
        if (empty($description) && empty($table_image)) {
            return null;
        }

        return [
            'description' => $description ?: '',
            'table_image' => $table_image,
            'table_alt' => $table_alt ?: 'Buyout details table',
            'has_image' => !empty($table_image),
            'has_content' => !empty($description) || !empty($table_image),
        ];
    }

    /**
     * STATIC HELPERS FOR USE IN TEMPLATES
     */

    /**
     * Check if buyout details are enabled for product
     */
    public static function isBuyoutEnabled($product_id): bool
    {
        return (bool) get_field('buyout_enabled', $product_id);
    }

    /**
     * Get buyout description
     */
    public static function getDescription($product_id): string
    {
        return get_field('buyout_description', $product_id) ?: '';
    }

    /**
     * Get buyout table image
     */
    public static function getTableImage($product_id): ?array
    {
        return get_field('buyout_table_image', $product_id);
    }

    /**
     * Check if product has buyout content
     */
    public static function hasBuyoutContent($product_id): bool
    {
        if (!self::isBuyoutEnabled($product_id)) {
            return false;
        }

        $description = self::getDescription($product_id);
        $image = self::getTableImage($product_id);

        return !empty($description) || !empty($image);
    }
}
