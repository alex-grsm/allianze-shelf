<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class AssetOverviewListTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Asset Overview List
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: ASSET OVERVIEW LIST =====
            [
                'key' => 'field_asset_overview_list_tab',
                'label' => 'Asset Overview List',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => create_acf_conditional_logic(['social_media_assets', 'newsletter', 'landing_page']),
            ],
            [
                'key' => 'field_asset_overview_list_enabled',
                'label' => 'Enable Asset Overview List Section',
                'name' => 'asset_overview_list_enabled',
                'type' => 'true_false',
                'instructions' => 'Enable to show asset overview list section on product page.',
                'required' => 0,
                'message' => 'Show asset overview list section on product page',
                'default_value' => 1,
                'ui' => 1,
                'ui_on_text' => 'Yes',
                'ui_off_text' => 'No',
                'conditional_logic' => create_acf_conditional_logic(['social_media_assets', 'newsletter', 'landing_page']),
            ],

            [
                'key' => 'field_asset_overview_list_items',
                'label' => 'Asset Overview List Items',
                'name' => 'asset_overview_list_items',
                'type' => 'repeater',
                'instructions' => 'Add items for asset overview list with description and images.',
                'required' => 0,
                'collapsed' => 'field_asset_overview_item_description',
                'min' => 0,
                'max' => 8,
                'layout' => 'block',
                'button_label' => 'Add Asset Overview Item',
                'conditional_logic' => create_acf_conditional_logic(['social_media_assets', 'newsletter', 'landing_page']),
                'sub_fields' => [
                    [
                        'key' => 'field_asset_overview_item_enabled',
                        'label' => 'Item Enabled',
                        'name' => 'item_enabled',
                        'type' => 'true_false',
                        'instructions' => 'Enable this item in overview list',
                        'wrapper' => ['width' => '100'],
                        'message' => 'Show in list',
                        'default_value' => 1,
                        'ui' => 1,
                        'ui_on_text' => 'Yes',
                        'ui_off_text' => 'No',
                    ],
                    [
                        'key' => 'field_asset_overview_item_description',
                        'label' => 'Item Description',
                        'name' => 'item_description',
                        'type' => 'textarea',
                        'instructions' => 'Enter detailed description for this asset overview item',
                        'required' => 1,
                        'rows' => 4,
                        'placeholder' => 'Lorem ipsum dolor sit amet...',
                        'maxlength' => 800,
                    ],
                    [
                        'key' => 'field_asset_overview_item_image',
                        'label' => 'Item Image',
                        'name' => 'item_image',
                        'type' => 'image',
                        'instructions' => 'Upload image for this asset overview item.',
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
        if (!self::isEnabledForProductType($product, ['social_media_assets', 'newsletter', 'landing_page'])) {
            return null;
        }

        $productId = $product->get_id();

        return [
            'asset_overview_list_enabled' => self::getBooleanFieldValue('asset_overview_list_enabled', $productId, true),
            'asset_overview_list_items' => self::getFormattedItems($product),
            'asset_overview_list_stats' => self::getItemsStats($product),
            'has_asset_overview_list_content' => self::hasAssetOverviewListContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['social_media_assets', 'newsletter', 'landing_page'])
            || !self::isAssetOverviewListEnabled($product)) {
            return ['assetOverviewList' => null];
        }

        $formatted_items = self::getFormattedItems($product);

        if (empty($formatted_items)) {
            return ['assetOverviewList' => null];
        }

        return [
            'assetOverviewList' => [
                'items' => $formatted_items,
                'total_count' => count($formatted_items),
                'has_items' => !empty($formatted_items),
            ]
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return ['assetOverviewList' => null];
    }

    /**
     * Зарегистрировать хуки
     */
    public static function registerHooks(): void
    {
        add_action('wp_insert_post', [self::class, 'setDefaultItems']);
        add_action('save_post_product', [self::class, 'ensureItemsOnPublish']);
    }

    // ===== PRIVATE METHODS =====

    /**
     * Проверить, включен ли asset overview list для продукта
     */
    private static function isAssetOverviewListEnabled(WC_Product $product): bool
    {
        return self::getBooleanFieldValue('asset_overview_list_enabled', $product->get_id(), true);
    }

    /**
     * Получить только активные items с изображениями и описанием
     */
    private static function getActiveItems(WC_Product $product): array
    {
        $items = self::getRepeaterFieldValue('asset_overview_list_items', $product->get_id());

        return array_filter($items, function($item) {
            return !empty($item['item_enabled'])
                && !empty($item['item_description'])
                && !empty($item['item_image']);
        });
    }

    /**
     * Получить отформатированные items для отображения
     */
    private static function getFormattedItems(WC_Product $product): array
    {
        $items = self::getActiveItems($product);
        $formatted_items = [];

        foreach ($items as $index => $item) {
            $imageData = self::formatImageData($item['item_image']);

            if ($imageData) {
                $formatted_items[] = [
                    'index' => $index, // Для чередования left/right в шаблоне
                    'description' => $item['item_description'],
                    'image' => $item['item_image'], // Оригинальные данные для совместимости
                    'image_data' => $imageData, // Новые форматированные данные
                    'slug' => 'item-' . ($index + 1),
                ];
            }
        }

        return $formatted_items;
    }

    /**
     * Получить статистику items
     */
    private static function getItemsStats(WC_Product $product): array
    {
        $allItems = self::getRepeaterFieldValue('asset_overview_list_items', $product->get_id());
        $activeItems = self::getActiveItems($product);

        return [
            'total' => count($allItems),
            'active' => count($activeItems),
            'has_items' => count($activeItems) > 0,
        ];
    }

    /**
     * Проверить, есть ли контент для asset overview list
     */
    private static function hasAssetOverviewListContent(WC_Product $product): bool
    {
        if (!self::isAssetOverviewListEnabled($product)) {
            return false;
        }

        $activeItems = self::getActiveItems($product);
        return count($activeItems) > 0;
    }



    /**
     * Получить дефолтные items
     */
    private static function getDefaultItems(): array
    {
        return [
            [
                'item_enabled' => true,
                'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
            ],
            [
                'item_enabled' => true,
                'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
            ],
            [
                'item_enabled' => true,
                'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
            ],
        ];
    }

    /**
     * Получить базовые items
     */
    private static function getBasicItems(): array
    {
        return [
            [
                'item_enabled' => true,
                'item_description' => 'Overview of the main assets and resources included in this product.',
            ],
        ];
    }

    /**
     * Проверить, есть ли данные для отображения
     */
    protected static function hasContent(WC_Product $product): bool
    {
        return self::hasAssetOverviewListContent($product);
    }

    // ===== STATIC METHODS FOR HOOKS =====

    /**
     * Установить дефолтные items при создании продукта
     */
    public static function setDefaultItems($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Только для новых продуктов типов social_media_assets, newsletter или landing_page
        if (get_post_status($post_id) === 'auto-draft' && !get_field('asset_overview_list_items', $post_id)) {
            $product_type = get_field('product_type', $post_id);

            if (in_array($product_type, ['social_media_assets', 'newsletter', 'landing_page'])) {
                update_field('asset_overview_list_items', self::getDefaultItems(), $post_id);
                update_field('asset_overview_list_enabled', true, $post_id);
            }
        }
    }

    /**
     * Убедиться, что опубликованный продукт имеет items
     */
    public static function ensureItemsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $product_type = get_field('product_type', $post_id);

        if (!in_array($product_type, ['social_media_assets', 'newsletter', 'landing_page'])) {
            return;
        }

        $items = get_field('asset_overview_list_items', $post_id);

        // Если нет items, добавляем базовые
        if (empty($items)) {
            update_field('asset_overview_list_items', self::getBasicItems(), $post_id);
        }
    }

    // ===== STATIC HELPER METHODS FOR TEMPLATES =====

    /**
     * Получить количество активных items для продукта
     */
    public static function getActiveItemsCount(int $product_id): int
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return 0;
        }

        $activeItems = self::getActiveItems($product);
        return count($activeItems);
    }

    /**
     * Проверить, есть ли items для отображения
     */
    public static function hasItems(int $product_id): bool
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }

        return self::hasAssetOverviewListContent($product);
    }

    /**
     * Получить первый активный item (для превью)
     */
    public static function getFirstActiveItem(int $product_id): ?array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return null;
        }

        $formattedItems = self::getFormattedItems($product);
        return !empty($formattedItems) ? $formattedItems[0] : null;
    }
}
