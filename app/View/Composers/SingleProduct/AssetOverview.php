<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class AssetOverview extends Composer
{
    protected static $views = [
        'partials.single-product.asset-overview',
    ];

    /**
     * Register ACF fields and hooks
     */
    public static function register()
    {
        add_action('acf/init', [self::class, 'registerFields']);
        add_action('wp_insert_post', [self::class, 'setDefaultAssets']);
        add_action('save_post_product', [self::class, 'ensureAssetsOnPublish']);
    }

    /**
     * Data for view
     */
    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['assetOverview' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['assetOverview' => null];
        }

        return [
            'assetOverview' => $this->getAssetData($product)
        ];
    }

    /**
     * Register ACF fields for asset overview
     */
    public static function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_asset_overview',
            'title' => 'Asset Overview',
            'fields' => [
                [
                    'key' => 'field_asset_description',
                    'label' => 'Asset Overview Description',
                    'name' => 'asset_description',
                    'type' => 'textarea',
                    'instructions' => 'Enter description for asset overview section.',
                    'required' => 0,
                    'rows' => 3,
                    'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                    'maxlength' => 400,
                ],
                [
                    'key' => 'field_product_assets',
                    'label' => 'Product Assets',
                    'name' => 'product_assets',
                    'type' => 'repeater',
                    'instructions' => 'Add assets with images and channel labels.',
                    'required' => 0,
                    'collapsed' => 'field_asset_label',
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add Asset',
                    'sub_fields' => [
                        [
                            'key' => 'field_asset_label',
                            'label' => 'Asset Label',
                            'name' => 'asset_label',
                            'type' => 'text',
                            'instructions' => 'Enter label for this asset (e.g.: Web, Social Media, Print)',
                            'required' => 1,
                            'wrapper' => ['width' => '50'],
                            'placeholder' => 'e.g.: Web',
                            'maxlength' => 50,
                        ],
                        [
                            'key' => 'field_asset_enabled',
                            'label' => 'Asset Enabled',
                            'name' => 'asset_enabled',
                            'type' => 'true_false',
                            'instructions' => 'Enable this asset in slider',
                            'wrapper' => ['width' => '50'],
                            'message' => 'Show in slider',
                            'default_value' => 1,
                            'ui' => 1,
                            'ui_on_text' => 'Yes',
                            'ui_off_text' => 'No',
                        ],
                        [
                            'key' => 'field_asset_image',
                            'label' => 'Asset Image',
                            'name' => 'asset_image',
                            'type' => 'image',
                            'instructions' => 'Upload image for this asset.',
                            'required' => 1,
                            'return_format' => 'array',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ],
                    ],
                ],
                [
                    'key' => 'field_assets_enabled',
                    'label' => 'Enable Asset Overview Section',
                    'name' => 'assets_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Enable to show asset overview section on product page.',
                    'required' => 0,
                    'message' => 'Show asset overview section on product page',
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
            'description' => 'Configure asset overview slider for product pages',
        ]);
    }

    /**
     * Set default assets when creating product
     */
    public static function setDefaultAssets($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Only for new products
        if (get_post_status($post_id) === 'auto-draft' && !get_field('product_assets', $post_id)) {
            $default_assets = [
                [
                    'asset_label' => 'Web',
                    'asset_enabled' => true,
                ],
                [
                    'asset_label' => 'Social Media',
                    'asset_enabled' => true,
                ],
                [
                    'asset_label' => 'Print',
                    'asset_enabled' => true,
                ],
            ];

            update_field('product_assets', $default_assets, $post_id);
            update_field('asset_description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', $post_id);
            update_field('assets_enabled', true, $post_id);
        }
    }

    /**
     * Ensure published product has assets
     */
    public static function ensureAssetsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $assets = get_field('product_assets', $post_id);

        // If no assets, add basic ones
        if (empty($assets)) {
            $basic_assets = [
                [
                    'asset_label' => 'Web',
                    'asset_enabled' => true,
                ],
                [
                    'asset_label' => 'Social Media',
                    'asset_enabled' => true,
                ],
            ];

            update_field('product_assets', $basic_assets, $post_id);
        }
    }

    /**
     * Get asset data for product (for view)
     */
    private function getAssetData(WC_Product $product): ?array
    {
        $product_id = $product->get_id();

        // Check if assets are enabled
        if (!self::isAssetsEnabled($product_id)) {
            return null;
        }

        $assets = self::getAssets($product_id);
        $description = get_field('asset_description', $product_id);

        if (empty($assets)) {
            return null;
        }

        // Format data for view - only enabled assets with images
        $formatted_assets = [];
        foreach ($assets as $asset) {
            if (!empty($asset['asset_enabled']) && !empty($asset['asset_image']) && !empty($asset['asset_label'])) {
                $formatted_assets[] = [
                    'label' => $asset['asset_label'],
                    'image' => $asset['asset_image'],
                    'slug' => sanitize_title($asset['asset_label']),
                ];
            }
        }

        if (empty($formatted_assets)) {
            return null;
        }

        return [
            'description' => $description ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            'assets' => $formatted_assets,
            'total_count' => count($formatted_assets),
            'has_assets' => !empty($formatted_assets),
        ];
    }

    /**
     * STATIC HELPERS FOR USE IN TEMPLATES
     */

    /**
     * Check if assets are enabled for product
     */
    public static function isAssetsEnabled($product_id): bool
    {
        return (bool) get_field('assets_enabled', $product_id);
    }

    /**
     * Get product assets
     */
    public static function getAssets($product_id): array
    {
        return get_field('product_assets', $product_id) ?: [];
    }

    /**
     * Get only active assets with images
     */
    public static function getActiveAssets($product_id): array
    {
        $assets = self::getAssets($product_id);

        return array_filter($assets, function($asset) {
            return !empty($asset['asset_enabled']) && !empty($asset['asset_image']);
        });
    }

    /**
     * Get assets statistics
     */
    public static function getStats($product_id): array
    {
        if (!self::isAssetsEnabled($product_id)) {
            return [
                'total' => 0,
                'active' => 0,
                'has_assets' => false,
            ];
        }

        $assets = self::getAssets($product_id);
        $active_assets = self::getActiveAssets($product_id);

        return [
            'total' => count($assets),
            'active' => count($active_assets),
            'has_assets' => count($active_assets) > 0,
        ];
    }

    /**
     * Check if product has assets with images
     */
    public static function hasAssetContent($product_id): bool
    {
        if (!self::isAssetsEnabled($product_id)) {
            return false;
        }

        $active_assets = self::getActiveAssets($product_id);
        return count($active_assets) > 0;
    }
}
