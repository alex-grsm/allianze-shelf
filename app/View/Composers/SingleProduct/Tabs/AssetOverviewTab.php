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
                'conditional_logic' => create_acf_conditional_logic(['companies']),
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
                'conditional_logic' => create_acf_conditional_logic(['companies']),
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
                'maxlength' => 3000,
                'conditional_logic' => create_acf_conditional_logic(['companies']),
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
                'conditional_logic' => create_acf_conditional_logic(['companies']),
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

        $productId = $product->get_id();

        return [
            'assets_enabled' => self::getBooleanFieldValue('assets_enabled', $productId, true),
            'asset_description' => self::getFieldValue('asset_description', $productId, self::getDefaultDescription()),
            'assets' => self::getFormattedAssets($product),
            'assets_stats' => self::getAssetsStats($product),
            'has_asset_content' => self::hasAssetContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies']) || !self::isAssetsEnabled($product)) {
            return ['assetOverview' => null];
        }

        $formatted_assets = self::getFormattedAssets($product);

        if (empty($formatted_assets)) {
            return ['assetOverview' => null];
        }

        return [
            'assetOverview' => [
                'description' => self::getFieldValue('asset_description', $product->get_id(), self::getDefaultDescription()),
                'assets' => $formatted_assets,
                'total_count' => count($formatted_assets),
                'has_assets' => !empty($formatted_assets),
            ]
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return ['assetOverview' => null];
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
     * Проверить, включены ли assets для продукта
     */
    private static function isAssetsEnabled(WC_Product $product): bool
    {
        return self::getBooleanFieldValue('assets_enabled', $product->get_id(), true);
    }

    /**
     * Получить только активные assets с изображениями
     */
    private static function getActiveAssets(WC_Product $product): array
    {
        $assets = self::getRepeaterFieldValue('product_assets', $product->get_id());

        return array_filter($assets, function($asset) {
            return !empty($asset['asset_enabled']) && !empty($asset['asset_image']);
        });
    }

    /**
     * Получить отформатированные assets для отображения
     */
    private static function getFormattedAssets(WC_Product $product): array
    {
        $assets = self::getActiveAssets($product);
        $formatted_assets = [];

        foreach ($assets as $asset) {
            if (!empty($asset['asset_label'])) {
                $imageData = self::formatImageData($asset['asset_image']);

                if ($imageData) {
                    $formatted_assets[] = [
                        'label' => $asset['asset_label'],
                        'image' => $asset['asset_image'], // Сохраняем оригинальные данные для совместимости
                        'image_data' => $imageData, // Новые форматированные данные
                        'slug' => self::createSlug($asset['asset_label']),
                    ];
                }
            }
        }

        return $formatted_assets;
    }

    /**
     * Получить статистику assets
     */
    private static function getAssetsStats(WC_Product $product): array
    {
        $allAssets = self::getRepeaterFieldValue('product_assets', $product->get_id());
        $activeAssets = self::getActiveAssets($product);

        return [
            'total' => count($allAssets),
            'active' => count($activeAssets),
            'has_assets' => count($activeAssets) > 0,
        ];
    }

    /**
     * Проверить, есть ли контент для assets
     */
    private static function hasAssetContent(WC_Product $product): bool
    {
        if (!self::isAssetsEnabled($product)) {
            return false;
        }

        $activeAssets = self::getActiveAssets($product);
        return count($activeAssets) > 0;
    }

    /**
     * Получить описание по умолчанию
     */
    private static function getDefaultDescription(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';
    }

    // ===== STATIC METHODS FOR HOOKS =====

    /**
     * Установить дефолтные assets при создании продукта
     */
    public static function setDefaultAssets($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Только для новых продуктов
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
            update_field('asset_description', self::getDefaultDescription(), $post_id);
            update_field('assets_enabled', true, $post_id);
        }
    }

    /**
     * Убедиться, что опубликованный продукт имеет assets
     */
    public static function ensureAssetsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $assets = get_field('product_assets', $post_id);

        // Если нет assets, добавляем базовые
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
