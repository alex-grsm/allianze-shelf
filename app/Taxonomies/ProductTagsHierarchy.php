<?php

namespace App\Taxonomies;

class ProductTagsHierarchy
{
    /**
     * Название таксономии
     */
    public const TAXONOMY_NAME = 'product_tags_hierarchy';

    /**
     * Регистрация таксономии
     */
    public static function register(): void
    {
        add_action('init', [self::class, 'registerTaxonomy']);
        add_action('wp_ajax_product_filter_by_tags', [self::class, 'handleAjaxFilter']);
        add_action('wp_ajax_nopriv_product_filter_by_tags', [self::class, 'handleAjaxFilter']);
    }

    /**
     * Регистрация таксономии
     */
    public static function registerTaxonomy(): void
    {
        register_taxonomy(self::TAXONOMY_NAME, 'product', [
            'hierarchical' => true,
            'labels' => [
                'name' => __('Product Tags Hierarchy', 'sage'),
                'singular_name' => __('Product Tag', 'sage'),
                'menu_name' => __('Tag Hierarchy', 'sage'),
                'all_items' => __('All Tags', 'sage'),
                'edit_item' => __('Edit Tag', 'sage'),
                'view_item' => __('View Tag', 'sage'),
                'update_item' => __('Update Tag', 'sage'),
                'add_new_item' => __('Add New Tag', 'sage'),
                'new_item_name' => __('New Tag Name', 'sage'),
                'parent_item' => __('Parent Tag', 'sage'),
                'parent_item_colon' => __('Parent Tag:', 'sage'),
                'search_items' => __('Search Tags', 'sage'),
                'not_found' => __('No tags found', 'sage'),
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => [
                'slug' => 'product-tags',
                'hierarchical' => true,
                'with_front' => false
            ],
            'meta_box_cb' => 'post_categories_meta_box',
            'capabilities' => [
                'manage_terms' => 'manage_woocommerce',
                'edit_terms' => 'manage_woocommerce',
                'delete_terms' => 'manage_woocommerce',
                'assign_terms' => 'edit_products',
            ],
        ]);
    }

    /**
     * Получить иерархические теги продукта
     */
    public static function getProductTags(int $productId): array
    {
        $terms = wp_get_post_terms($productId, self::TAXONOMY_NAME, [
            'orderby' => 'parent',
            'order' => 'ASC'
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        return array_map(function($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'parent' => $term->parent,
                'level' => self::getTermLevel($term->term_id),
                'url' => get_term_link($term),
                'ancestors' => get_ancestors($term->term_id, self::TAXONOMY_NAME)
            ];
        }, $terms);
    }

    /**
     * Получить уровень термина в иерархии
     */
    private static function getTermLevel(int $termId): int
    {
        $ancestors = get_ancestors($termId, self::TAXONOMY_NAME);
        return count($ancestors);
    }

    /**
     * Получить все главные категории
     */
    public static function getMainCategories(): array
    {
        $terms = get_terms([
            'taxonomy' => self::TAXONOMY_NAME,
            'parent' => 0,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        return array_map(function($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
                'children' => self::getChildrenTerms($term->term_id)
            ];
        }, $terms);
    }

    /**
     * Получить дочерние термины
     */
    public static function getChildrenTerms(int $parentId): array
    {
        $terms = get_terms([
            'taxonomy' => self::TAXONOMY_NAME,
            'parent' => $parentId,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        return array_map(function($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
                'url' => get_term_link($term)
            ];
        }, $terms);
    }

    /**
     * AJAX обработчик фильтрации продуктов
     */
    public static function handleAjaxFilter(): void
    {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'product_filter_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        $tagIds = array_map('intval', $_POST['tag_ids'] ?? []);

        if (empty($tagIds)) {
            wp_send_json_error('No tags selected');
        }

        $args = [
            'post_type' => 'product',
            'posts_per_page' => 12,
            'paged' => intval($_POST['page'] ?? 1),
            'tax_query' => [
                [
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field' => 'term_id',
                    'terms' => $tagIds,
                    'operator' => 'IN'
                ]
            ]
        ];

        $query = new \WP_Query($args);
        $products = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());

                if ($product) {
                    $products[] = [
                        'id' => $product->get_id(),
                        'title' => $product->get_name(),
                        'url' => $product->get_permalink(),
                        'price' => $product->get_price_html(),
                        'image' => wp_get_attachment_image_src($product->get_image_id(), 'medium'),
                        'tags' => self::getProductTags($product->get_id())
                    ];
                }
            }
        }

        wp_reset_postdata();

        wp_send_json_success([
            'products' => $products,
            'total_pages' => $query->max_num_pages,
            'total_posts' => $query->found_posts
        ]);
    }

    /**
     * Добавить теги к продукту
     */
    public static function addTagsToProduct(int $productId, array $tagIds): bool
    {
        $result = wp_set_post_terms($productId, $tagIds, self::TAXONOMY_NAME, false);
        return !is_wp_error($result);
    }

    /**
     * Проверить, есть ли у продукта конкретный тег
     */
    public static function productHasTag(int $productId, string $tagSlug): bool
    {
        return has_term($tagSlug, self::TAXONOMY_NAME, $productId);
    }

    /**
     * Получить продукты по тегу
     */
    public static function getProductsByTag(string $tagSlug, int $limit = -1): array
    {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'tax_query' => [
                [
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field' => 'slug',
                    'terms' => $tagSlug
                ]
            ]
        ];

        $query = new \WP_Query($args);
        $products = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());

                if ($product) {
                    $products[] = $product;
                }
            }
        }

        wp_reset_postdata();
        return $products;
    }
}
