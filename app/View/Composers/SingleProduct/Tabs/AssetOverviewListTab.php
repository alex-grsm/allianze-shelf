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
                'key' => 'field_asset_overview_list_description',
                'label' => 'Section Description',
                'name' => 'asset_overview_list_description',
                'type' => 'textarea',
                'instructions' => 'Enter description for the Asset Overview section. This will appear under the main title.',
                'required' => 0,
                'rows' => 3,
                'placeholder' => 'Describe what this asset overview contains...',
                'maxlength' => 3000,
                'conditional_logic' => create_acf_conditional_logic(['social_media_assets', 'newsletter', 'landing_page']),
            ],
            [
                'key' => 'field_asset_overview_list_items',
                'label' => 'Asset Overview List Items',
                'name' => 'asset_overview_list_items',
                'type' => 'repeater',
                'instructions' => 'Add items for asset overview list with description and media (image or video).',
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
                        'maxlength' => 5000,
                    ],
                    [
                        'key' => 'field_asset_overview_item_media_type',
                        'label' => 'Media Type',
                        'name' => 'item_media_type',
                        'type' => 'select',
                        'instructions' => 'Choose the type of media for this item',
                        'required' => 1,
                        'choices' => [
                            'image' => 'Image',
                            'video' => 'Video',
                        ],
                        'default_value' => 'image',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'return_format' => 'value',
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
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_asset_overview_item_media_type',
                                    'operator' => '==',
                                    'value' => 'image',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_asset_overview_item_video',
                        'label' => 'Item Video',
                        'name' => 'item_video',
                        'type' => 'file',
                        'instructions' => 'Upload video file for this asset overview item. Supported formats: MP4, WebM, AVI, MOV. Custom video controls will be used automatically.',
                        'required' => 1,
                        'return_format' => 'array',
                        'library' => 'all',
                        'mime_types' => 'mp4,webm,avi,mov,m4v',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_asset_overview_item_media_type',
                                    'operator' => '==',
                                    'value' => 'video',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_asset_overview_item_video_poster',
                        'label' => 'Video Poster Image',
                        'name' => 'item_video_poster',
                        'type' => 'image',
                        'instructions' => 'Upload poster image for the video (optional). This will be shown before the video plays.',
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_asset_overview_item_media_type',
                                    'operator' => '==',
                                    'value' => 'video',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_asset_overview_item_video_settings',
                        'label' => 'Video Settings',
                        'name' => 'item_video_settings',
                        'type' => 'checkbox',
                        'instructions' => 'Select video playback options. Custom video controls will be used automatically.',
                        'required' => 0,
                        'choices' => [
                            'autoplay' => 'Autoplay video (only works when muted)',
                            'loop' => 'Loop video continuously',
                            'muted' => 'Muted by default',
                        ],
                        'default_value' => ['muted'], // Лучше по умолчанию muted для автоплея
                        'layout' => 'vertical',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_asset_overview_item_media_type',
                                    'operator' => '==',
                                    'value' => 'video',
                                ],
                            ],
                        ],
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
            'asset_overview_list_description' => self::getFieldValue('asset_overview_list_description', $productId, self::getDefaultDescription()),
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
                'description' => self::getFieldValue('asset_overview_list_description', $product->get_id(), self::getDefaultDescription()),
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
     * Получить только активные items с медиа и описанием
     */
    private static function getActiveItems(WC_Product $product): array
    {
        $items = self::getRepeaterFieldValue('asset_overview_list_items', $product->get_id());

        return array_filter($items, function($item) {
            if (empty($item['item_enabled']) || empty($item['item_description'])) {
                return false;
            }

            $media_type = $item['item_media_type'] ?? 'image';

            // Проверяем наличие нужного медиа в зависимости от типа
            if ($media_type === 'video') {
                return !empty($item['item_video']);
            } else {
                return !empty($item['item_image']);
            }
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
            $media_type = $item['item_media_type'] ?? 'image';
            $formatted_item = [
                'index' => $index,
                'description' => $item['item_description'],
                'media_type' => $media_type,
                'slug' => 'item-' . ($index + 1),
            ];

            if ($media_type === 'video') {
                $formatted_item['video'] = $item['item_video'];
                $formatted_item['video_poster'] = $item['item_video_poster'] ?? null;
                $formatted_item['video_settings'] = $item['item_video_settings'] ?? ['muted'];
                $formatted_item['video_data'] = self::formatVideoData($item['item_video'], $item['item_video_settings'] ?? ['muted']);
            } else {
                $imageData = self::formatImageData($item['item_image']);
                if (!$imageData) {
                    continue; // Пропускаем, если нет изображения
                }
                $formatted_item['image'] = $item['item_image'];
                $formatted_item['image_data'] = $imageData;
            }

            $formatted_items[] = $formatted_item;
        }

        return $formatted_items;
    }

    /**
     * Форматировать данные видео
     */
    private static function formatVideoData(array $video, array $settings = ['muted']): array
    {
        return [
            'url' => $video['url'] ?? '',
            'filename' => $video['filename'] ?? '',
            'title' => $video['title'] ?? '',
            'alt' => $video['alt'] ?? '',
            'mime_type' => $video['mime_type'] ?? '',
            'filesize' => $video['filesize'] ?? 0,
            // Всегда используем кастомные контролы
            'has_controls' => false, // HTML5 контролы отключены
            'use_custom_controls' => true, // Всегда используем кастомные
            'autoplay' => in_array('autoplay', $settings),
            'loop' => in_array('loop', $settings),
            'muted' => in_array('muted', $settings),
        ];
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
     * Получить описание по умолчанию
     */
    private static function getDefaultDescription(): string
    {
        return 'Explore the comprehensive collection of assets included in this product. Each item has been carefully crafted to provide maximum value and functionality.';
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

        // Только для новых продуктов
        if (get_post_status($post_id) === 'auto-draft' && !get_field('asset_overview_list_items', $post_id)) {
            $default_items = [
                [
                    'item_enabled' => true,
                    'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
                    'item_media_type' => 'image',
                ],
                [
                    'item_enabled' => true,
                    'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
                    'item_media_type' => 'image',
                ],
                [
                    'item_enabled' => true,
                    'item_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
                    'item_media_type' => 'image',
                ],
            ];

            update_field('asset_overview_list_items', $default_items, $post_id);
            update_field('asset_overview_list_description', self::getDefaultDescription(), $post_id);
            update_field('asset_overview_list_enabled', true, $post_id);
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

        $items = get_field('asset_overview_list_items', $post_id);

        // Если нет items, добавляем базовые
        if (empty($items)) {
            $basic_items = [
                [
                    'item_enabled' => true,
                    'item_description' => 'Overview of the main assets and resources included in this product.',
                    'item_media_type' => 'image',
                ],
            ];

            update_field('asset_overview_list_items', $basic_items, $post_id);
        }
    }
}
