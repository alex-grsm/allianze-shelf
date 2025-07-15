<?php

use App\Taxonomies\ProductTagsHierarchy;

/**
 * Theme helpers
 */

/**
 * Product type constants
 */
define('PRODUCT_TYPE_COMPANIES', 'companies');
define('PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS', 'social_media_assets');
define('PRODUCT_TYPE_NEWSLETTER', 'newsletter');
define('PRODUCT_TYPE_LANDING_PAGE', 'landing_page');

/**
 * Get all product types
 */
function get_all_product_types(): array
{
    return [
        PRODUCT_TYPE_COMPANIES,
        PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS,
        PRODUCT_TYPE_NEWSLETTER,
        PRODUCT_TYPE_LANDING_PAGE,
    ];
}

/**
 * Get product type labels
 */
function get_product_type_labels(): array
{
    return [
        PRODUCT_TYPE_COMPANIES => 'Campaigns',
        PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS => 'Social Media Assets',
        PRODUCT_TYPE_NEWSLETTER => 'Newsletter',
        PRODUCT_TYPE_LANDING_PAGE => 'Landing Page',
    ];
}

/**
 * Get display names for frontend
 */
function get_product_type_display_names(): array
{
    return [
        PRODUCT_TYPE_COMPANIES => 'Brand campaign',
        PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS => 'Social Media campaign',
        PRODUCT_TYPE_NEWSLETTER => 'Newsletter campaign',
        PRODUCT_TYPE_LANDING_PAGE => 'Landing Page campaign',
    ];
}

/**
 * Get product names
 */
function get_product_type_product_names(): array
{
    return [
        PRODUCT_TYPE_COMPANIES => 'Campaigns',
        PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS => 'Social Media',
        PRODUCT_TYPE_NEWSLETTER => 'Newsletter',
        PRODUCT_TYPE_LANDING_PAGE => 'Landing Page',
    ];
}

/**
 * Get field prefix for product type
 */
function get_product_field_prefix(string $productType): string
{
    return match ($productType) {
        PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS => 'sma_',
        PRODUCT_TYPE_NEWSLETTER => 'newsletter_',
        PRODUCT_TYPE_LANDING_PAGE => 'landing_page_',
        default => '',
    };
}

if (!function_exists('flag_url')) {
    function flag_url($countryCode) {
        if (empty($countryCode)) {
            return Vite::asset('resources/images/icons/flag.svg');
        }

        return 'https://purecatamphetamine.github.io/country-flag-icons/1x1/' . strtoupper($countryCode) . '.svg';
    }
}

/**
 * Get complete list of countries with codes
 */
function get_country_choices(): array
{
    return [
        'AT' => 'Austria (AT)',
        'AU' => 'Australia (AU)',
        'BG' => 'Bulgaria (BG)',
        'BR' => 'Brazil (BR)',
        'CH' => 'Switzerland (CH)',
        'CO' => 'Colombia (CO)',
        'CZ' => 'Czech Republic (CZ)',
        'DE' => 'Germany (DE)',
        'ES' => 'Spain (ES)',
        'FR' => 'France (FR)',
        'GB' => 'United Kingdom (GB)',
        'HR' => 'Croatia (HR)',
        'HU' => 'Hungary (HU)',
        'ID' => 'Indonesia (ID)',
        'IE' => 'Ireland (IE)',
        'IT' => 'Italy (IT)',
        'LK' => 'Sri Lanka (LK)',
        'MX' => 'Mexico (MX)',
        'MY' => 'Malaysia (MY)',
        'NL' => 'Netherlands (NL)',
        'PL' => 'Poland (PL)',
        'PT' => 'Portugal (PT)',
        'SG' => 'Singapore (SG)',
        'SI' => 'Slovenia (SI)',
        'SK' => 'Slovakia (SK)',
        'TH' => 'Thailand (TH)',
        'TR' => 'Turkey (TR)',
        'US' => 'United States (US)',
    ];
}

/**
 * Get product type from product
 */
function get_product_type(WC_Product|int $product): string
{
    $product_id = $product instanceof WC_Product ? $product->get_id() : $product;
    return get_field('product_type', $product_id) ?: PRODUCT_TYPE_COMPANIES;
}

/**
 * Get content type labels
 */
function get_content_type_labels(): array
{
    return [
        'video' => 'Video',
        'audio' => 'Audio',
        'text' => 'Text',
    ];
}

/**
 * Get content type display name
 */
function get_content_type_display_name(string $contentType): string
{
    $labels = get_content_type_labels();
    return $labels[$contentType] ?? 'Video';
}

/**
 * Get product meta data unified across all product types
 */
function get_product_meta_data(WC_Product|int $product): array
{
    $product_obj = $product instanceof WC_Product ? $product : wc_get_product($product);
    if (!$product_obj) {
        return get_empty_product_meta_data();
    }

    $product_type = get_product_type($product_obj);
    $prefix = get_product_field_prefix($product_type);

    $display_names = get_product_type_display_names();
    $product_names = get_product_type_product_names();

    // Получаем тип контента
    $content_type = get_field("{$prefix}content_type", $product_obj->get_id()) ?: 'video';

    return [
        'product_type' => $product_type,
        'country_code' => get_field("{$prefix}product_country_code", $product_obj->get_id()) ?: '',
        'country_flag_url' => get_country_flag_url($product_obj),
        'content_type' => $content_type,
        'content_type_label' => get_content_type_display_name($content_type),
        'rights_until_date' => get_field("{$prefix}rights_until_date", $product_obj->get_id()),
        'rights_until_formatted' => get_formatted_rights_date($product_obj),
        'target' => get_field("{$prefix}product_target", $product_obj->get_id()) ?: '',
        'year' => get_field("{$prefix}product_year", $product_obj->get_id()) ?: '',
        'buyout' => get_field("{$prefix}product_buyout", $product_obj->get_id()) ?: '',
        'label' => get_field("{$prefix}product_label", $product_obj->get_id()) ?: '',
        'display_name' => $display_names[$product_type] ?? 'Brand campaign',
        'product_name' => $product_names[$product_type] ?? 'Car',
    ];
}

/**
 * Get empty product meta data structure
 */
function get_empty_product_meta_data(): array
{
    return [
        'product_type' => PRODUCT_TYPE_COMPANIES,
        'country_code' => '',
        'country_flag_url' => flag_url(''),
        'content_type' => 'video',
        'content_type_label' => 'Video',
        'rights_until_date' => null,
        'rights_until_formatted' => '',
        'target' => '',
        'year' => '',
        'buyout' => '',
        'label' => '',
        'display_name' => 'Brand campaign',
        'product_name' => 'Car',
    ];
}

/**
 * Get country flag URL for product
 */
function get_country_flag_url(WC_Product $product): string
{
    $product_type = get_product_type($product);
    $prefix = get_product_field_prefix($product_type);
    $country_code = get_field("{$prefix}product_country_code", $product->get_id());

    return flag_url($country_code);
}

/**
 * Get formatted rights date for product
 */
function get_formatted_rights_date(WC_Product $product): string
{
    $product_type = get_product_type($product);
    $prefix = get_product_field_prefix($product_type);
    $date = get_field("{$prefix}rights_until_date", $product->get_id());

    if (!$date) {
        return '';
    }

    // If it's a DateTime object
    if ($date instanceof \DateTime) {
        return $date->format('m/Y');
    }

    // If it's a string
    if (is_string($date)) {
        $timestamp = strtotime($date);
        return $timestamp ? date('m/Y', $timestamp) : '';
    }

    return '';
}

/**
 * Create conditional logic for ACF fields
 */
function create_acf_conditional_logic(array $conditions): array
{
    $logic = [];

    foreach ($conditions as $condition) {
        if (is_string($condition)) {
            // Simple product type condition
            $logic[] = [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $condition,
                ],
            ];
        } elseif (is_array($condition) && isset($condition['product_type'])) {
            // Product type with additional field condition
            $condition_array = [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $condition['product_type'],
                ],
            ];

            if (isset($condition['field'])) {
                $condition_array[] = [
                    'field' => $condition['field'],
                    'operator' => $condition['operator'] ?? '==',
                    'value' => $condition['value'] ?? '1',
                ];
            }

            $logic[] = $condition_array;
        }
    }

    return $logic;
}

/**
 * Create ACF conditional logic for multiple product types
 */
function create_acf_conditional_logic_for_types(array $productTypes, ?array $additionalCondition = null): array
{
    $conditions = [];

    foreach ($productTypes as $productType) {
        if ($additionalCondition) {
            $conditions[] = [
                'product_type' => $productType,
                'field' => $additionalCondition['field'],
                'operator' => $additionalCondition['operator'] ?? '==',
                'value' => $additionalCondition['value'] ?? '1',
            ];
        } else {
            $conditions[] = $productType;
        }
    }

    return create_acf_conditional_logic($conditions);
}

/**
 * Check if product type supports feature
 */
function product_type_supports(string $productType, string $feature): bool
{
    $features = [
        'asset_overview' => [PRODUCT_TYPE_COMPANIES],
        'asset_overview_list' => [PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS, PRODUCT_TYPE_NEWSLETTER, PRODUCT_TYPE_LANDING_PAGE],
        'channels' => [PRODUCT_TYPE_COMPANIES],
        'buyout_details' => get_all_product_types(),
        'links' => get_all_product_types(),
        'attachments' => get_all_product_types(),
    ];

    return in_array($productType, $features[$feature] ?? []);
}

/**
 * Check if current product supports feature
 */
function current_product_supports(WC_Product $product, string $feature): bool
{
    $productType = get_product_type($product);
    return product_type_supports($productType, $feature);
}

/**
 * Format file size in human readable format
 */
function format_file_size(int $bytes): string
{
    if ($bytes == 0) return '0 B';

    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));

    return round($bytes / pow($k, $i), 1) . ' ' . $sizes[$i];
}

/**
 * Get default ACF values for product type
 */
function get_default_acf_values(string $productType): array
{
    return [
        'assets_enabled' => product_type_supports($productType, 'asset_overview'),
        'asset_overview_list_enabled' => product_type_supports($productType, 'asset_overview_list'),
        'channels_enabled' => product_type_supports($productType, 'channels'),
        'buyout_enabled' => true,
        'links_enabled' => true,
        'attachments_enabled' => true,
    ];
}

/**
 * Create standard info fields for product type
 */
function create_product_info_fields(string $productType, string $tabName): array
{
    $prefix = get_product_field_prefix($productType);
    $conditionalLogic = create_acf_conditional_logic([$productType]);

    return [
        [
            'key' => "field_{$productType}_info_tab",
            'label' => $tabName,
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}product_country_code",
            'label' => 'Country of Origin',
            'name' => "{$prefix}product_country_code",
            'type' => 'select',
            'choices' => get_country_choices(),
            'default_value' => '',
            'allow_null' => 1,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => 'Select country...',
            'instructions' => 'Select the country where the product is manufactured',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}content_type",
            'label' => 'Content Type',
            'name' => "{$prefix}content_type",
            'type' => 'select',
            'choices' => [
                'video' => 'Video',
                'audio' => 'Audio',
                'text' => 'Text',
            ],
            'default_value' => 'video',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => 'Select content type...',
            'instructions' => 'Select the primary content type for this product',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}rights_until_date",
            'label' => 'Rights Valid Until',
            'name' => "{$prefix}rights_until_date",
            'type' => 'date_picker',
            'display_format' => 'm/Y',
            'return_format' => 'Y-m-d',
            'first_day' => 1,
            'instructions' => 'Select the expiration date for product rights',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}product_target",
            'label' => 'Target',
            'name' => "{$prefix}product_target",
            'type' => 'text',
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => 'Enter target information...',
            'instructions' => 'Specify the target information for this product',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}product_year",
            'label' => 'Year',
            'name' => "{$prefix}product_year",
            'type' => 'text',
            'default_value' => '',
            'maxlength' => 4,
            'placeholder' => 'YYYY',
            'instructions' => 'Enter the year for this product',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}product_buyout",
            'label' => 'Buyout',
            'name' => "{$prefix}product_buyout",
            'type' => 'text',
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => 'Enter buyout information...',
            'instructions' => 'Specify the buyout information for this product',
            'conditional_logic' => $conditionalLogic,
        ],
        [
            'key' => "field_{$prefix}product_label",
            'label' => 'Product Label',
            'name' => "{$prefix}product_label",
            'type' => 'select',
            'choices' => [
                'featured' => 'Featured',
                'easy_adaptable' => 'Easy adaptable',
            ],
            'default_value' => '',
            'allow_null' => 1,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => 'Select label...',
            'instructions' => 'Choose a label for this product',
            'conditional_logic' => $conditionalLogic,
        ],
    ];
}

/**
 * =========================================
 * PRODUCT TAGS HELPER FUNCTIONS (MINIMAL)
 * =========================================
 */

/**
 * Получить все теги продукта в читаемом формате
 */
function get_product_tags_display(int $product_id, string $separator = ', '): string
{
    $tags = ProductTagsHierarchy::getProductTags($product_id);

    if (empty($tags)) {
        return '';
    }

    $tag_names = array_map(function($tag) {
        return $tag['name'];
    }, $tags);

    return implode($separator, $tag_names);
}

/**
 * Проверить, есть ли у продукта конкретный тег
 */
function product_has_tag(int $product_id, string $tag_slug): bool
{
    return ProductTagsHierarchy::productHasTag($product_id, $tag_slug);
}

/**
 * Получить теги для отображения в карточке продукта
 */
function get_product_card_tags(int $product_id, int $limit = 3): array
{
    $tags = ProductTagsHierarchy::getProductTags($product_id);

    if (empty($tags)) {
        return [];
    }

    // Сортируем по уровню (сначала родительские)
    usort($tags, function($a, $b) {
        return $a['level'] - $b['level'];
    });

    // Ограничиваем количество
    return array_slice($tags, 0, $limit);
}

/**
 * Получить связанные продукты по тегам
 */
function get_related_products_by_tags(int $product_id, int $limit = 4): array
{
    $product_tags = ProductTagsHierarchy::getProductTags($product_id);

    if (empty($product_tags)) {
        return [];
    }

    $tag_ids = array_column($product_tags, 'id');

    $args = [
        'post_type' => 'product',
        'posts_per_page' => $limit + 1, // +1 чтобы исключить текущий продукт
        'post_status' => 'publish',
        'post__not_in' => [$product_id],
        'tax_query' => [
            [
                'taxonomy' => ProductTagsHierarchy::TAXONOMY_NAME,
                'field' => 'term_id',
                'terms' => $tag_ids,
                'operator' => 'IN'
            ]
        ],
        'orderby' => 'rand'
    ];

    $query = new WP_Query($args);
    $products = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if ($product && count($products) < $limit) {
                $products[] = $product;
            }
        }
    }

    wp_reset_postdata();
    return $products;
}

/**
 * Получить основные категории тегов
 */
function get_product_tag_categories(): array
{
    return ProductTagsHierarchy::getMainCategories();
}

/**
 * Получить все теги определенной категории
 */
function get_product_tags_by_category(string $category_slug): array
{
    $category = get_term_by('slug', $category_slug, ProductTagsHierarchy::TAXONOMY_NAME);

    if (!$category || is_wp_error($category)) {
        return [];
    }

    return ProductTagsHierarchy::getChildrenTerms($category->term_id);
}
