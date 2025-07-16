<?php

namespace App\Helpers;

class MenuHelper
{
    /**
     * Ключ кеша для главного меню
     */
    private const MAIN_MENU_CACHE_KEY = 'main_menu_categories';

    /**
     * Ключ кеша для мобильного меню
     */
    private const MOBILE_MENU_CACHE_KEY = 'mobile_menu_categories';

    /**
     * Группа кеша
     */
    private const CACHE_GROUP = 'menu_cache';

    /**
     * Время кеширования (1 час)
     */
    private const CACHE_TIME = HOUR_IN_SECONDS;

    /**
     * Регистрация всех хуков и фильтров для MenuHelper
     */
    public static function register(): void
    {
        // Хуки для очистки кеша меню при изменении категорий
        add_action('created_product_cat', [self::class, 'clearMenuCache']);
        add_action('edited_product_cat', [self::class, 'clearMenuCache']);
        add_action('deleted_product_cat', [self::class, 'clearMenuCache']);

        // Очистка кеша при изменении продуктов
        add_action('save_post_product', [self::class, 'clearMenuCache']);
        add_action('delete_post', [self::class, 'onProductDelete']);

        // Очистка кеша при изменении изображений категорий
        add_action('updated_term_meta', [self::class, 'onCategoryImageUpdate'], 10, 3);
    }

    /**
     * Получить меню для главного хедера
     */
    public static function getMainMenu(): array
    {
        $cached = wp_cache_get(self::MAIN_MENU_CACHE_KEY, self::CACHE_GROUP);

        if ($cached !== false) {
            return $cached;
        }

        $menu = self::buildMainMenu();
        wp_cache_set(self::MAIN_MENU_CACHE_KEY, $menu, self::CACHE_GROUP, self::CACHE_TIME);

        return $menu;
    }

    /**
     * Получить меню для мобильной версии
     */
    public static function getMobileMenu(): array
    {
        $cached = wp_cache_get(self::MOBILE_MENU_CACHE_KEY, self::CACHE_GROUP);

        if ($cached !== false) {
            return $cached;
        }

        $menu = self::buildMobileMenu();
        wp_cache_set(self::MOBILE_MENU_CACHE_KEY, $menu, self::CACHE_GROUP, self::CACHE_TIME);

        return $menu;
    }

    /**
     * Построить главное меню
     */
    private static function buildMainMenu(): array
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false, // Показываем все категории, даже пустые
            'parent' => 0,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'number' => 10, // Увеличим лимит
            'exclude' => self::getExcludedCategories(),
        ]);

        if (is_wp_error($categories) || empty($categories)) {
            return [];
        }

        $menu = [];
        foreach ($categories as $category) {
            $subcategories = self::getSubcategories($category->term_id, 10);

            $menu[] = [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'url' => get_term_link($category),
                'count' => $category->count,
                'subcategories' => $subcategories,
                'has_subcategories' => !empty($subcategories),
            ];
        }

        return $menu;
    }

    /**
     * Построить мобильное меню
     */
    private static function buildMobileMenu(): array
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false, // Показываем все категории, даже пустые
            'parent' => 0,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'number' => 10, // Увеличим лимит
            'exclude' => self::getExcludedCategories(),
        ]);

        if (is_wp_error($categories) || empty($categories)) {
            return [];
        }

        $menu = [];
        foreach ($categories as $category) {
            $subcategories = self::getSubcategories($category->term_id, 8);

            $menu[] = [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'url' => get_term_link($category),
                'count' => $category->count,
                'subcategories' => $subcategories,
                'has_subcategories' => !empty($subcategories),
            ];
        }

        return $menu;
    }

    /**
     * Получить подкатегории для категории
     */
    private static function getSubcategories(int $parentId, int $limit = 10): array
    {
        $subcategories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false, // Показываем все подкатегории, даже пустые
            'parent' => $parentId,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'number' => $limit
        ]);

        if (is_wp_error($subcategories) || empty($subcategories)) {
            return [];
        }

        $result = [];
        foreach ($subcategories as $subcategory) {
            $result[] = [
                'id' => $subcategory->term_id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'url' => get_term_link($subcategory),
                'count' => $subcategory->count,
            ];
        }

        return $result;
    }

    /**
     * Получить список исключаемых категорий
     */
    private static function getExcludedCategories(): array
    {
        $excluded = [];

        // Исключаем "Uncategorized"
        $uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
        if ($uncategorized && !is_wp_error($uncategorized)) {
            $excluded[] = $uncategorized->term_id;
        }

        return apply_filters('menu_helper_excluded_categories', $excluded);
    }

    /**
     * Очистить кеш меню
     */
    public static function clearMenuCache(): void
    {
        wp_cache_delete(self::MAIN_MENU_CACHE_KEY, self::CACHE_GROUP);
        wp_cache_delete(self::MOBILE_MENU_CACHE_KEY, self::CACHE_GROUP);
    }

    /**
     * Обработчик удаления продукта
     */
    public static function onProductDelete(int $postId): void
    {
        if (get_post_type($postId) === 'product') {
            self::clearMenuCache();
        }
    }

    /**
     * Обработчик изменения изображения категории
     */
    public static function onCategoryImageUpdate(int $metaId, int $objectId, string $metaKey): void
    {
        if ($metaKey === 'thumbnail_id') {
            $term = get_term($objectId);
            if ($term && !is_wp_error($term) && $term->taxonomy === 'product_cat') {
                self::clearMenuCache();
            }
        }
    }
}
