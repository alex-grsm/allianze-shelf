<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class AssetOverviewTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Asset Overview
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: ASSET OVERVIEW =====
            [
                'key' => 'field_asset_overview_tab',
                'label' => 'Asset Overview',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
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
                'conditional_logic' => self::getConditionalLogicForProductType('companies'),
            ],
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
                'conditional_logic' => self::getConditionalLogicForProductTypeAndField('companies', 'field_assets_enabled', '1'),
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
                'conditional_logic' => self::getConditionalLogicForProductTypeAndField('companies', 'field_assets_enabled', '1'),
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
            // Asset Overview
            'assets_enabled' => self::isAssetsEnabled($product),
            'asset_description' => self::getAssetDescription($product),
            'assets' => self::getActiveAssetsFormatted($product),
            'has_asset_content' => self::hasAssetContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return [
            'assetOverview' => self::getAssetOverviewForTemplate($product),
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return [
            'assetOverview' => null,
        ];
    }

    /**
     * Зарегистрировать хуки
     */
    public static function registerHooks(): void
    {
        add_action('wp_insert_post', [self::class, 'setDefaultAssets']);
        add_action('save_post_product', [self::class, 'ensureAssetsOnPublish']);
    }

    // ===== PRIVATE METHODS =====

    /**
     * Check if assets are enabled for product
     */
    private static function isAssetsEnabled(WC_Product $product): bool
    {
        return (bool) get_field('assets_enabled', $product->get_id());
    }

    /**
     * Get asset description
     */
    private static function getAssetDescription(WC_Product $product): string
    {
        return get_field('asset_description', $product->get_id()) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';
    }

    /**
     * Get product assets
     */
    private static function getAssets(WC_Product $product): array
    {
        return get_field('product_assets', $product->get_id()) ?: [];
    }

    /**
     * Get only active assets with images
     */
    private static function getActiveAssets(WC_Product $product): array
    {
        $assets = self::getAssets($product);

        return array_filter($assets, function($asset) {
            return !empty($asset['asset_enabled']) && !empty($asset['asset_image']);
        });
    }

    /**
     * Get active assets formatted for view
     */
    private static function getActiveAssetsFormatted(WC_Product $product): array
    {
        $assets = self::getActiveAssets($product);
        $formatted_assets = [];

        foreach ($assets as $asset) {
            if (!empty($asset['asset_label'])) {
                $formatted_assets[] = [
                    'label' => $asset['asset_label'],
                    'image' => $asset['asset_image'],
                    'slug' => sanitize_title($asset['asset_label']),
                ];
            }
        }

        return $formatted_assets;
    }

    /**
     * Check if product has asset content
     */
    private static function hasAssetContent(WC_Product $product): bool
    {
        if (!self::isAssetsEnabled($product)) {
            return false;
        }

        $active_assets = self::getActiveAssets($product);
        return count($active_assets) > 0;
    }

    /**
     * Get asset overview data formatted for template
     */
    private static function getAssetOverviewForTemplate(WC_Product $product): ?array
    {
        // Проверяем, включены ли ассеты и тип продукта
        if (!self::isEnabledForProductType($product, ['companies']) || !self::isAssetsEnabled($product)) {
            return null;
        }

        $formatted_assets = self::getActiveAssetsFormatted($product);

        if (empty($formatted_assets)) {
            return null;
        }

        return [
            'description' => self::getAssetDescription($product),
            'assets' => $formatted_assets,
            'total_count' => count($formatted_assets),
            'has_assets' => !empty($formatted_assets),
        ];
    }

    // ===== STATIC METHODS FOR HOOKS =====

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
}
