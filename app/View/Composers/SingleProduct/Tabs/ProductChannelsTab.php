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
                'conditional_logic' => create_acf_conditional_logic(['companies']),
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

        $stats = self::getChannelsStats($product);

        return [
            'productChannels' => [
                'channels' => $formatted_channels,
                'total_count' => $stats['total'],
                'included_count' => $stats['included'],
                'has_channels' => $stats['has_channels'],
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
        return self::getBooleanFieldValue('channels_enabled', $product->get_id(), true);
    }

    /**
     * Получить отформатированные channels для отображения
     */
    private static function getFormattedChannels(WC_Product $product): array
    {
        $channels = self::getRepeaterFieldValue('product_channels', $product->get_id());
        $formatted_channels = [];

        foreach ($channels as $channel) {
            if (!empty($channel['channel_name'])) {
                $formatted_channels[] = [
                    'name' => $channel['channel_name'],
                    'included' => (bool) ($channel['channel_included'] ?? false),
                    'slug' => self::createSlug($channel['channel_name'])
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

    /**
     * Получить дефолтные каналы
     */
    private static function getDefaultChannels(): array
    {
        return [
            ['channel_name' => 'Web', 'channel_included' => true],
            ['channel_name' => 'Newsletter', 'channel_included' => false],
            ['channel_name' => 'Social Media', 'channel_included' => true],
            ['channel_name' => 'Digital', 'channel_included' => false],
            ['channel_name' => 'Print', 'channel_included' => false],
            ['channel_name' => 'OOH', 'channel_included' => false],
            ['channel_name' => 'POS', 'channel_included' => true],
            ['channel_name' => 'TV/Radio', 'channel_included' => true],
        ];
    }

    /**
     * Получить базовые каналы
     */
    private static function getBasicChannels(): array
    {
        return [
            ['channel_name' => 'Web', 'channel_included' => true],
            ['channel_name' => 'Social Media', 'channel_included' => true],
            ['channel_name' => 'POS', 'channel_included' => true],
        ];
    }

    /**
     * Проверить, есть ли данные для отображения
     */
    protected static function hasContent(WC_Product $product): bool
    {
        return self::hasChannelsContent($product);
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
            update_field('product_channels', self::getDefaultChannels(), $post_id);
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
            update_field('product_channels', self::getBasicChannels(), $post_id);
        }
    }

    // ===== STATIC HELPER METHODS FOR TEMPLATES =====

    /**
     * Проверить, активен ли канал для продукта
     */
    public static function isChannelActive(int $product_id, string $channel_name): bool
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }

        $channels = self::getRepeaterFieldValue('product_channels', $product_id);

        foreach ($channels as $channel) {
            if (strtolower($channel['channel_name'] ?? '') === strtolower($channel_name)) {
                return !empty($channel['channel_included']);
            }
        }

        return false;
    }

    /**
     * Получить только активные каналы продукта
     */
    public static function getActiveChannels(int $product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        $channels = self::getRepeaterFieldValue('product_channels', $product_id);

        return array_filter($channels, function($channel) {
            return !empty($channel['channel_included']);
        });
    }

    /**
     * Получить список названий каналов через запятую
     */
    public static function getChannelsList(int $product_id, bool $active_only = true, string $separator = ', '): string
    {
        $channels = $active_only
            ? self::getActiveChannels($product_id)
            : self::getRepeaterFieldValue('product_channels', $product_id);

        $names = array_filter(array_column($channels, 'channel_name'));

        return implode($separator, $names);
    }
}
