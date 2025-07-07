<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class ProductChannels extends Composer
{
    protected static $views = [
        'partials.single-product.product-channels',
    ];

    /**
     * Register ACF fields and hooks
     */
    public static function register()
    {
        add_action('acf/init', [self::class, 'registerFields']);
        add_action('wp_insert_post', [self::class, 'setDefaultChannels']);
        add_action('save_post_product', [self::class, 'ensureChannelsOnPublish']);
    }

    /**
     * Data for view
     */
    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['productChannels' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['productChannels' => null];
        }

        return [
            'productChannels' => $this->getChannelsData($product)
        ];
    }

    /**
     * Register ACF fields for channels
     */
    public static function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_product_channels',
            'title' => 'Channels and Touchpoints',
            'fields' => [
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
            'description' => 'Configure channels and touchpoints for product',
        ]);
    }

    /**
     * Set default channels when creating product
     */
    public static function setDefaultChannels($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Only for new products
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
     * Ensure published product has channels
     */
    public static function ensureChannelsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $channels = get_field('product_channels', $post_id);

        // If no channels, add basic ones
        if (empty($channels)) {
            $basic_channels = [
                ['channel_name' => 'Web', 'channel_included' => true],
                ['channel_name' => 'Social Media', 'channel_included' => true],
                ['channel_name' => 'POS', 'channel_included' => true],
            ];

            update_field('product_channels', $basic_channels, $post_id);
        }
    }

    /**
     * Get channel data for product (for view)
     */
    private function getChannelsData(WC_Product $product): ?array
    {
        $product_id = $product->get_id();

        // Check if channels are enabled
        if (!self::isChannelsEnabled($product_id)) {
            return null;
        }

        // Get channels
        $channels = self::getChannels($product_id);

        if (empty($channels)) {
            return null;
        }

        // Format data for view
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

        if (empty($formatted_channels)) {
            return null;
        }

        return [
            'channels' => $formatted_channels,
            'total_count' => count($formatted_channels),
            'included_count' => count(array_filter($formatted_channels, function($channel) {
                return $channel['included'];
            })),
            'has_channels' => !empty($formatted_channels),
            'visible_limit' => 5
        ];
    }

    /**
     * STATIC HELPERS FOR USE IN TEMPLATES
     */

    /**
     * Check if channels are enabled for product
     */
    public static function isChannelsEnabled($product_id): bool
    {
        return (bool) get_field('channels_enabled', $product_id);
    }

    /**
     * Get product channels
     */
    public static function getChannels($product_id)
    {
        return get_field('product_channels', $product_id) ?: [];
    }

    /**
     * Check if channel is active for product
     */
    public static function isChannelActive($product_id, $channel_name)
    {
        $channels = self::getChannels($product_id);

        foreach ($channels as $channel) {
            if (strtolower($channel['channel_name']) === strtolower($channel_name)) {
                return !empty($channel['channel_included']);
            }
        }

        return false;
    }

    /**
     * Get channel statistics
     */
    public static function getStats($product_id)
    {
        if (!self::isChannelsEnabled($product_id)) {
            return [
                'total' => 0,
                'included' => 0,
                'coverage' => 0,
                'has_channels' => false
            ];
        }

        $channels = self::getChannels($product_id);

        if (empty($channels)) {
            return [
                'total' => 0,
                'included' => 0,
                'coverage' => 0,
                'has_channels' => false
            ];
        }

        $included_count = count(array_filter($channels, function($channel) {
            return !empty($channel['channel_included']);
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
     * Get only active product channels
     */
    public static function getActiveChannels($product_id)
    {
        $channels = self::getChannels($product_id);

        return array_filter($channels, function($channel) {
            return !empty($channel['channel_included']);
        });
    }

    /**
     * Get list of channel names separated by comma
     */
    public static function getChannelsList($product_id, $active_only = true, $separator = ', ')
    {
        $channels = $active_only
            ? self::getActiveChannels($product_id)
            : self::getChannels($product_id);

        $names = array_column($channels, 'channel_name');

        return implode($separator, $names);
    }
}
