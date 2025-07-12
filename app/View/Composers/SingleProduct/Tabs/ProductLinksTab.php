<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class ProductLinksTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Product Links
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: PRODUCT LINKS =====
            [
                'key' => 'field_product_links_tab',
                'label' => 'Product Links',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets', 'newsletter']),
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
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets', 'newsletter']),
            ],
            [
                'key' => 'field_links_description',
                'label' => 'Links Description',
                'name' => 'links_description',
                'type' => 'text',
                'instructions' => 'Enter description for links section.',
                'required' => 0,
                'placeholder' => 'The following links give you access to...',
                'maxlength' => 200,
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets', 'newsletter'], 'field_links_enabled', '1'),
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
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets', 'newsletter'], 'field_links_enabled', '1'),
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
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter'])) {
            return null;
        }

        return [
            // Product Links
            'links_enabled' => self::isLinksEnabled($product),
            'links_description' => self::getLinksDescription($product),
            'links' => self::getFormattedLinks($product),
            'links_stats' => self::getLinksStats($product),
            'has_links_content' => self::hasLinksContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return [
            'productLinks' => self::getLinksForTemplate($product),
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return [
            'productLinks' => null,
        ];
    }

    /**
     * Зарегистрировать хуки
     */
    public static function registerHooks(): void
    {
        add_action('wp_insert_post', [self::class, 'setDefaultLinks']);
        add_action('save_post_product', [self::class, 'ensureLinksOnPublish']);
    }

    // ===== PRIVATE METHODS =====

    /**
     * Check if links are enabled for product
     */
    private static function isLinksEnabled(WC_Product $product): bool
    {
        return (bool) get_field('links_enabled', $product->get_id());
    }

    /**
     * Get links description
     */
    private static function getLinksDescription(WC_Product $product): string
    {
        return get_field('links_description', $product->get_id()) ?: 'The following links give you access to additional resources and information';
    }

    /**
     * Get product links
     */
    private static function getLinks(WC_Product $product): array
    {
        return get_field('product_links', $product->get_id()) ?: [];
    }

    /**
     * Get formatted links for view
     */
    private static function getFormattedLinks(WC_Product $product): array
    {
        $links = self::getLinks($product);
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

        return $formatted_links;
    }

    /**
     * Get links statistics
     */
    private static function getLinksStats(WC_Product $product): array
    {
        $links = self::getLinks($product);
        $active_links = self::getActiveLinks($product);

        return [
            'total' => count($links),
            'active' => count($active_links),
            'has_links' => count($active_links) > 0,
        ];
    }

    /**
     * Get only active links
     */
    private static function getActiveLinks(WC_Product $product): array
    {
        $links = self::getLinks($product);

        return array_filter($links, function($link) {
            return !empty($link['link_enabled']) && !empty($link['link_title']);
        });
    }

    /**
     * Check if product has links content
     */
    private static function hasLinksContent(WC_Product $product): bool
    {
        if (!self::isLinksEnabled($product)) {
            return false;
        }

        $active_links = self::getActiveLinks($product);
        return count($active_links) > 0;
    }

    /**
     * Get links data formatted for template (compatibility with existing product-links.blade.php)
     */
    private static function getLinksForTemplate(WC_Product $product): ?array
    {
        // Проверяем, включены ли ссылки и тип продукта
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter']) || !self::isLinksEnabled($product)) {
            return null;
        }

        $formatted_links = self::getFormattedLinks($product);

        if (empty($formatted_links)) {
            return null;
        }

        return [
            'description' => self::getLinksDescription($product),
            'links' => $formatted_links,
            'total_count' => count($formatted_links),
            'has_links' => !empty($formatted_links),
            'visible_limit' => 6,
        ];
    }

    // ===== STATIC METHODS FOR HOOKS =====

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
            update_field('links_enabled', true, $post_id);
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

    // ===== STATIC HELPER METHODS FOR TEMPLATES =====

    /**
     * Get links for product (static method for template use)
     */
    public static function getLinksForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        return self::getLinks($product);
    }

    /**
     * Get active links for product (static method for template use)
     */
    public static function getActiveLinksForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        return self::getActiveLinks($product);
    }

    /**
     * Get stats for product (static method for template use)
     */
    public static function getStatsForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [
                'total' => 0,
                'active' => 0,
                'has_links' => false,
            ];
        }

        return self::getLinksStats($product);
    }

    /**
     * Check if links are enabled for product (static method for template use)
     */
    public static function isLinksEnabledForProduct($product_id): bool
    {
        return (bool) get_field('links_enabled', $product_id);
    }
}
