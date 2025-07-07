<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class ProductLinks extends Composer
{
    protected static $views = [
        'partials.single-product.product-links',
    ];

    /**
     * Register ACF fields and hooks
     */
    public static function register()
    {
        add_action('acf/init', [self::class, 'registerFields']);
        add_action('wp_insert_post', [self::class, 'setDefaultLinks']);
        add_action('save_post_product', [self::class, 'ensureLinksOnPublish']);
    }

    /**
     * Data for view
     */
    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['productLinks' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['productLinks' => null];
        }

        return [
            'productLinks' => $this->getLinksData($product)
        ];
    }

    /**
     * Register ACF fields for product links
     */
    public static function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_product_links',
            'title' => 'Product Links',
            'fields' => [
                [
                    'key' => 'field_links_description',
                    'label' => 'Links Description',
                    'name' => 'links_description',
                    'type' => 'text',
                    'instructions' => 'Enter description for links section.',
                    'required' => 0,
                    'placeholder' => 'The following links give you access to...',
                    'maxlength' => 200,
                ],
                [
                    'key' => 'field_product_links',
                    'label' => 'Product Links',
                    'name' => 'product_links',
                    'type' => 'repeater',
                    'instructions' => 'Add links with descriptions and logos.',
                    'required' => 0,
                    'collapsed' => 'field_link_title',
                    'min' => 0,
                    'max' => 12,
                    'layout' => 'block',
                    'button_label' => 'Add Link',
                    'sub_fields' => [
                        [
                            'key' => 'field_link_title',
                            'label' => 'Link Title',
                            'name' => 'link_title',
                            'type' => 'text',
                            'instructions' => 'Enter link title',
                            'required' => 1,
                            'wrapper' => ['width' => '50'],
                            'placeholder' => 'e.g.: Adobe CRM',
                            'maxlength' => 100,
                        ],
                        [
                            'key' => 'field_link_enabled',
                            'label' => 'Link Enabled',
                            'name' => 'link_enabled',
                            'type' => 'true_false',
                            'instructions' => 'Enable this link',
                            'wrapper' => ['width' => '50'],
                            'message' => 'Show this link',
                            'default_value' => 1,
                            'ui' => 1,
                            'ui_on_text' => 'Yes',
                            'ui_off_text' => 'No',
                        ],
                        [
                            'key' => 'field_link_description',
                            'label' => 'Link Description',
                            'name' => 'link_description',
                            'type' => 'textarea',
                            'instructions' => 'Enter detailed description for this link',
                            'required' => 0,
                            'rows' => 4,
                            'placeholder' => 'Lorem ipsum dolor sit amet...',
                            'maxlength' => 500,
                        ],
                        [
                            'key' => 'field_link_logo',
                            'label' => 'Link Logo/Image',
                            'name' => 'link_logo',
                            'type' => 'image',
                            'instructions' => 'Upload logo or image for this link.',
                            'required' => 0,
                            'return_format' => 'array',
                            'preview_size' => 'thumbnail',
                            'library' => 'all',
                            'wrapper' => ['width' => '50'],
                        ],
                        [
                            'key' => 'field_link_url',
                            'label' => 'Link URL',
                            'name' => 'link_url',
                            'type' => 'url',
                            'instructions' => 'Enter URL for this link',
                            'required' => 0,
                            'wrapper' => ['width' => '50'],
                            'placeholder' => 'https://example.com',
                        ],
                        [
                            'key' => 'field_link_target',
                            'label' => 'Open in New Tab',
                            'name' => 'link_target',
                            'type' => 'true_false',
                            'instructions' => 'Open link in new tab/window',
                            'required' => 0,
                            'message' => 'Open in new tab',
                            'default_value' => 1,
                            'ui' => 1,
                            'ui_on_text' => 'Yes',
                            'ui_off_text' => 'No',
                        ],
                    ],
                ],
                [
                    'key' => 'field_links_enabled',
                    'label' => 'Enable Links Section',
                    'name' => 'links_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Enable to show links section on product page.',
                    'required' => 0,
                    'message' => 'Show links section on product page',
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
            'description' => 'Configure links section for product pages',
        ]);
    }

    /**
     * Set default links when creating product
     */
    public static function setDefaultLinks($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Only for new products
        if (get_post_status($post_id) === 'auto-draft' && !get_field('product_links', $post_id)) {
            $default_links = [
                [
                    'link_title' => 'Media Analysis',
                    'link_enabled' => true,
                    'link_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
                    'link_url' => '#',
                    'link_target' => true,
                ],
                [
                    'link_title' => 'CRM Platform',
                    'link_enabled' => true,
                    'link_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
                    'link_url' => '#',
                    'link_target' => true,
                ],
                [
                    'link_title' => 'Experience Manager',
                    'link_enabled' => true,
                    'link_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
                    'link_url' => '#',
                    'link_target' => true,
                ],
            ];

            update_field('product_links', $default_links, $post_id);
            update_field('links_description', 'The following links give you access to list pages, media analyses and CRM information', $post_id);
        }
    }

    /**
     * Ensure published product has links
     */
    public static function ensureLinksOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $links = get_field('product_links', $post_id);

        // If no links, add basic ones
        if (empty($links)) {
            $basic_links = [
                [
                    'link_title' => 'Information Portal',
                    'link_enabled' => true,
                    'link_description' => 'Access additional product information and resources.',
                    'link_url' => '#',
                    'link_target' => true,
                ],
            ];

            update_field('product_links', $basic_links, $post_id);
        }
    }

    /**
     * Get links data for product (for view)
     */
    private function getLinksData(WC_Product $product): ?array
    {
        $product_id = $product->get_id();

        // Check if links are enabled
        if (!self::isLinksEnabled($product_id)) {
            return null;
        }

        $links = self::getLinks($product_id);
        $description = get_field('links_description', $product_id);

        if (empty($links)) {
            return null;
        }

        // Format data for view
        $formatted_links = [];
        foreach ($links as $link) {
            if (!empty($link['link_title']) && !empty($link['link_enabled'])) {
                $formatted_links[] = [
                    'title' => $link['link_title'],
                    'description' => $link['link_description'] ?: '',
                    'url' => $link['link_url'] ?: '#',
                    'target' => !empty($link['link_target']) ? '_blank' : '_self',
                    'logo' => $link['link_logo'] ?: null,
                    'slug' => sanitize_title($link['link_title']),
                    'has_logo' => !empty($link['link_logo']),
                    'has_url' => !empty($link['link_url']) && $link['link_url'] !== '#',
                ];
            }
        }

        if (empty($formatted_links)) {
            return null;
        }

        return [
            'description' => $description ?: 'The following links give you access to additional resources and information',
            'links' => $formatted_links,
            'total_count' => count($formatted_links),
            'has_links' => !empty($formatted_links),
            'visible_limit' => 6,
        ];
    }

    /**
     * STATIC HELPERS FOR USE IN TEMPLATES
     */

    /**
     * Check if links are enabled for product
     */
    public static function isLinksEnabled($product_id): bool
    {
        return (bool) get_field('links_enabled', $product_id);
    }

    /**
     * Get product links
     */
    public static function getLinks($product_id): array
    {
        return get_field('product_links', $product_id) ?: [];
    }

    /**
     * Get only active links
     */
    public static function getActiveLinks($product_id): array
    {
        $links = self::getLinks($product_id);

        return array_filter($links, function($link) {
            return !empty($link['link_enabled']) && !empty($link['link_title']);
        });
    }

    /**
     * Get links statistics
     */
    public static function getStats($product_id): array
    {
        $links = self::getLinks($product_id);
        $active_links = self::getActiveLinks($product_id);

        return [
            'total' => count($links),
            'active' => count($active_links),
            'has_links' => count($active_links) > 0,
        ];
    }
}
