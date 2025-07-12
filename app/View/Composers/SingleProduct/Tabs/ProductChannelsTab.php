<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class ProductChannelsTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Product Channels
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: PRODUCT CHANNELS =====
            [
                'key' => 'field_product_channels_tab',
                'label' => 'Channels & Touchpoints',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => create_acf_conditional_logic(['companies']),
            ],
            [
                'key' => 'field_channels_enabled',
                'label' => 'Enable Channels Section',
                'name' => 'channels_enabled',
                'type' => 'true_false',
                'instructions' => 'Enable to show channels section on product page.',
                'required' => 0,
                'message' => 'Show channels section on product page',
                'default_value' => 1,
                'ui' => 1,
                'ui_on_text' => 'Yes',
                'ui_off_text' => 'No',
                'conditional_logic' => create_acf_conditional_logic(['companies']),
            ],
            [
                'key' => 'field_product_channels',
                'label' => 'Product Channels',
                'name' => 'product_channels',
                'type' => 'repeater',
                'instructions' => 'Add channels and specify which assets are included in each channel.',
                'required' => 0,
                'collapsed' => 'field_channel_name',
                'min' => 0,
                'max' => 20,
                'layout' => 'block',
                'button_label' => 'Add Channel',
                'conditional_logic' => create_acf_conditional_logic(['companies'], 'field_channels_enabled', '1'),
                'sub_fields' => [
                    [
                        'key' => 'field_channel_name',
                        'label' => 'Channel Name',
                        'name' => 'channel_name',
                        'type' => 'text',
                        'instructions' => 'Enter channel name (e.g.: Web, Digital, Print)',
                        'required' => 1,
                        'wrapper' => ['width' => '70'],
                        'placeholder' => 'e.g.: Web',
                        'maxlength' => 100,
                    ],
                    [
                        'key' => 'field_channel_included',
                        'label' => 'Assets included',
                        'name' => 'channel_included',
                        'type' => 'true_false',
                        'instructions' => 'Check if assets are included in this channel',
                        'wrapper' => ['width' => '30'],
                        'message' => 'Included in product',
                        'default_value' => 0,
                        'ui' => 1,
                        'ui_on_text' => 'Yes',
                        'ui_off_text' => 'No',
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
            'channels_enabled' => self::isChannelsEnabled($product),
            'channels' => self::getFormattedChannels($product),
            'channels_stats' => self::getChannelsStats($product),
            'has_channels_content' => self::hasChannelsContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies']) || !self::isChannelsEnabled($product)) {
            return ['productChannels' => null];
        }

        $formatted_channels = self::getFormattedChannels($product);

        if (empty($formatted_channels)) {
            return ['productChannels' => null];
        }

        return [
            'productChannels' => [
                'channels' => $formatted_channels,
                'total_count' => count($formatted_channels),
                'included_count' => count(array_filter($formatted_channels, function($channel) {
                    return $channel['included'];
                })),
                'has_channels' => !empty($formatted_channels),
                'visible_limit' => 5
            ]
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return ['productChannels' => null];
    }

    /**
     * Зарегистрировать хуки
     */
    public static function registerHooks(): void
    {
        add_action('wp_insert_post', [self::class, 'setDefaultChannels']);
        add_action('save_post_product', [self::class, 'ensureChannelsOnPublish']);
    }

    // ===== PRIVATE METHODS =====

    /**
     * Проверить, включены ли channels для продукта
     */
    private static function isChannelsEnabled(WC_Product $product): bool
    {
        return (bool) get_field('channels_enabled', $product->get_id());
    }

    /**
     * Получить channels продукта
     */
    private static function getChannels(WC_Product $product): array
    {
        return get_field('product_channels', $product->get_id()) ?: [];
    }

    /**
     * Получить отформатированные channels для отображения
     */
    private static function getFormattedChannels(WC_Product $product): array
    {
        $channels = self::getChannels($product);
        $formatted_channels = [];

        foreach ($channels as $channel) {
            if (!empty($channel['channel_name'])) {
                $formatted_channels[] = [
                    'name' => $channel['channel_name'],
                    'included' => (bool) ($channel['channel_included'] ?? false),
                    'slug' => sanitize_title($channel['channel_name'])
                ];
            }
        }

        return $formatted_channels;
    }

    /**
     * Получить статистику channels
     */
    private static function getChannelsStats(WC_Product $product): array
    {
        if (!self::isChannelsEnabled($product)) {
            return [
                'total' => 0,
                'included' => 0,
                'coverage' => 0,
                'has_channels' => false
            ];
        }

        $channels = self::getFormattedChannels($product);

        if (empty($channels)) {
            return [
                'total' => 0,
                'included' => 0,
                'coverage' => 0,
                'has_channels' => false
            ];
        }

        $included_count = count(array_filter($channels, function($channel) {
            return $channel['included'];
        }));

        $total_count = count($channels);

        return [
            'total' => $total_count,
            'included' => $included_count,
            'coverage' => $total_count > 0 ? round(($included_count / $total_count) * 100, 1) : 0,
            'has_channels' => $total_count > 0
        ];
    }

    /**
     * Проверить, есть ли контент для channels
     */
    private static function hasChannelsContent(WC_Product $product): bool
    {
        if (!self::isChannelsEnabled($product)) {
            return false;
        }

        $channels = self::getFormattedChannels($product);
        return count($channels) > 0;
    }

    // ===== STATIC METHODS FOR HOOKS =====

    /**
     * Установить дефолтные channels при создании продукта
     */
    public static function setDefaultChannels($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Только для новых продуктов
        if (get_post_status($post_id) === 'auto-draft' && !get_field('product_channels', $post_id)) {
            $default_channels = [
                ['channel_name' => 'Web', 'channel_included' => true],
                ['channel_name' => 'Newsletter', 'channel_included' => false],
                ['channel_name' => 'Social Media', 'channel_included' => true],
                ['channel_name' => 'Digital', 'channel_included' => false],
                ['channel_name' => 'Print', 'channel_included' => false],
                ['channel_name' => 'OOH', 'channel_included' => false],
                ['channel_name' => 'POS', 'channel_included' => true],
                ['channel_name' => 'TV/Radio', 'channel_included' => true],
            ];

            update_field('product_channels', $default_channels, $post_id);
            update_field('channels_enabled', true, $post_id);
        }
    }

    /**
     * Убедиться, что опубликованный продукт имеет channels
     */
    public static function ensureChannelsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $channels = get_field('product_channels', $post_id);

        // Если нет channels, добавляем базовые
        if (empty($channels)) {
            $basic_channels = [
                ['channel_name' => 'Web', 'channel_included' => true],
                ['channel_name' => 'Social Media', 'channel_included' => true],
                ['channel_name' => 'POS', 'channel_included' => true],
            ];

            update_field('product_channels', $basic_channels, $post_id);
        }
    }

    // ===== STATIC HELPER METHODS FOR TEMPLATES =====

    /**
     * Проверить, активен ли канал для продукта
     */
    public static function isChannelActive($product_id, $channel_name): bool
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }

        $channels = self::getChannels($product);

        foreach ($channels as $channel) {
            if (strtolower($channel['channel_name']) === strtolower($channel_name)) {
                return !empty($channel['channel_included']);
            }
        }

        return false;
    }

    /**
     * Получить только активные каналы продукта
     */
    public static function getActiveChannels($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        $channels = self::getChannels($product);

        return array_filter($channels, function($channel) {
            return !empty($channel['channel_included']);
        });
    }

    /**
     * Получить список названий каналов через запятую
     */
    public static function getChannelsList($product_id, $active_only = true, $separator = ', '): string
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return '';
        }

        $channels = $active_only
            ? self::getActiveChannels($product_id)
            : self::getChannels($product);

        $names = array_column($channels, 'channel_name');

        return implode($separator, $names);
    }
}
