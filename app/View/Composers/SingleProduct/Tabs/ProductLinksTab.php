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
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter', 'landing_page'])) {
            return null;
        }

        $productId = $product->get_id();

        return [
            'links_enabled' => self::getBooleanFieldValue('links_enabled', $productId, true),
            'links_description' => self::getFieldValue('links_description', $productId, self::getDefaultDescription()),
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
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter', 'landing_page'])
            || !self::isLinksEnabled($product)) {
            return ['productLinks' => null];
        }

        $formatted_links = self::getFormattedLinks($product);

        if (empty($formatted_links)) {
            return ['productLinks' => null];
        }

        return [
            'productLinks' => [
                'description' => self::getFieldValue('links_description', $product->get_id(), self::getDefaultDescription()),
                'links' => $formatted_links,
                'total_count' => count($formatted_links),
                'has_links' => !empty($formatted_links),
                'visible_limit' => 6,
            ]
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return ['productLinks' => null];
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
     * Проверить, включены ли links для продукта
     */
    private static function isLinksEnabled(WC_Product $product): bool
    {
        return self::getBooleanFieldValue('links_enabled', $product->get_id(), true);
    }

    /**
     * Получить только активные links
     */
    private static function getActiveLinks(WC_Product $product): array
    {
        $links = self::getRepeaterFieldValue('product_links', $product->get_id());

        return array_filter($links, function($link) {
            return !empty($link['link_enabled']) && !empty($link['link_title']);
        });
    }

    /**
     * Получить отформатированные links для отображения
     */
    private static function getFormattedLinks(WC_Product $product): array
    {
        $links = self::getActiveLinks($product);
        $formatted_links = [];

        foreach ($links as $link) {
            if (!empty($link['link_title'])) {
                $logoData = null;
                if (!empty($link['link_logo'])) {
                    $logoData = self::formatImageData($link['link_logo']);
                }

                $formatted_links[] = [
                    'title' => $link['link_title'],
                    'description' => $link['link_description'] ?? '',
                    'url' => $link['link_url'] ?? '#',
                    'target' => !empty($link['link_target']) ? '_blank' : '_self',
                    'logo' => $link['link_logo'] ?? null, // Оригинальные данные для совместимости
                    'logo_data' => $logoData, // Новые форматированные данные
                    'slug' => self::createSlug($link['link_title']),
                    'has_logo' => !empty($logoData),
                    'has_url' => !empty($link['link_url']) && $link['link_url'] !== '#',
                ];
            }
        }

        return $formatted_links;
    }

    /**
     * Получить статистику links
     */
    private static function getLinksStats(WC_Product $product): array
    {
        $allLinks = self::getRepeaterFieldValue('product_links', $product->get_id());
        $activeLinks = self::getActiveLinks($product);

        return [
            'total' => count($allLinks),
            'active' => count($activeLinks),
            'has_links' => count($activeLinks) > 0,
        ];
    }

    /**
     * Проверить, есть ли контент для links
     */
    private static function hasLinksContent(WC_Product $product): bool
    {
        if (!self::isLinksEnabled($product)) {
            return false;
        }

        $activeLinks = self::getActiveLinks($product);
        return count($activeLinks) > 0;
    }

    /**
     * Получить описание по умолчанию
     */
    private static function getDefaultDescription(): string
    {
        return 'The following links give you access to additional resources and information';
    }

    /**
     * Получить дефолтные ссылки
     */
    private static function getDefaultLinks(): array
    {
        return [
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
    }

    /**
     * Получить базовые ссылки
     */
    private static function getBasicLinks(): array
    {
        return [
            [
                'link_title' => 'Information Portal',
                'link_enabled' => true,
                'link_description' => 'Access additional product information and resources.',
                'link_url' => '#',
                'link_target' => true,
            ],
        ];
    }

    /**
     * Проверить, есть ли данные для отображения
     */
    protected static function hasContent(WC_Product $product): bool
    {
        return self::hasLinksContent($product);
    }

    // ===== STATIC METHODS FOR HOOKS =====

    /**
     * Установить дефолтные links при создании продукта
     */
    public static function setDefaultLinks($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Только для новых продуктов
        if (get_post_status($post_id) === 'auto-draft' && !get_field('product_links', $post_id)) {
            update_field('product_links', self::getDefaultLinks(), $post_id);
            update_field('links_description', self::getDefaultDescription(), $post_id);
            update_field('links_enabled', true, $post_id);
        }
    }

    /**
     * Убедиться, что опубликованный продукт имеет links
     */
    public static function ensureLinksOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $links = get_field('product_links', $post_id);

        // Если нет links, добавляем базовые
        if (empty($links)) {
            update_field('product_links', self::getBasicLinks(), $post_id);
        }
    }
}
