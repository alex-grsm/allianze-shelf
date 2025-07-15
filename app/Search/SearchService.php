<?php

namespace App\Search;

use Exception;

class SearchService
{
    /**
     * Инициализация функций поиска
     */
    public static function register(): void
    {
        // Основной поиск
        add_action('pre_get_posts', [self::class, 'enhanceSearchQuery']);

        // Очистка фильтров после выполнения запроса
        add_action('wp', [self::class, 'removeSearchFilters']);

        // AJAX поиск
        add_action('wp_ajax_search_products', [self::class, 'ajaxSearchProducts']);
        add_action('wp_ajax_nopriv_search_products', [self::class, 'ajaxSearchProducts']);

        // JavaScript переменные
        add_action('wp_enqueue_scripts', [self::class, 'addSearchAjaxVars']);

        // Релевантность
        add_filter('posts_results', [self::class, 'improveSearchRelevance'], 10, 2);

        // Кеширование
        add_filter('posts_results', [self::class, 'cacheSearchResults'], 10, 2);
        add_filter('posts_pre_query', [self::class, 'getCachedSearchResults'], 10, 2);
        add_action('save_post', [self::class, 'clearSearchCache']);
        add_action('delete_post', [self::class, 'clearSearchCache']);

        // Очистка кеша при изменении терминов
        add_action('edited_product_cat', [self::class, 'clearSearchCacheOnTermUpdate'], 10, 3);
        add_action('edited_product_tag', [self::class, 'clearSearchCacheOnTermUpdate'], 10, 3);
        add_action('created_product_cat', [self::class, 'clearSearchCacheOnTermUpdate'], 10, 3);
        add_action('created_product_tag', [self::class, 'clearSearchCacheOnTermUpdate'], 10, 3);

        // Логирование популярных поисков
        add_action('template_redirect', [self::class, 'logSearchQuery']);

        // SEO и мета-данные
        add_action('wp_head', [self::class, 'searchSeoMeta'], 1);
        add_action('wp_head', [self::class, 'addSearchSchema']);

        // Отладка
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('wp_footer', [self::class, 'addSearchDebugInfo']);
            add_action('pre_get_posts', [self::class, 'debugSearchQuery'], 9999);
            add_action('template_redirect', [self::class, 'testSearchFunctionality']);
        }
    }

    /**
     * Улучшение основного поиска WordPress для поиска только продуктов
     */
    public static function enhanceSearchQuery($query)
    {
        // Проверяем, что это основной запрос поиска и не админка
        if (!is_admin() && $query->is_main_query() && $query->is_search()) {
            // Ищем только продукты
            $query->set('post_type', ['product']);

            // Исключаем вариации продуктов и другие не нужные типы
            $query->set('post_status', 'publish');

            // Добавляем мета-запрос для исключения вариаций продуктов и скрытых товаров
            $meta_query = $query->get('meta_query') ?: [];

            // Исключаем вариации продуктов
            $meta_query[] = [
                'relation' => 'AND',
                [
                    'key' => '_visibility',
                    'value' => 'hidden',
                    'compare' => '!='
                ],
                [
                    'relation' => 'OR',
                    [
                        'key' => '_visibility',
                        'value' => ['visible', 'catalog', 'search'],
                        'compare' => 'IN'
                    ],
                    [
                        'key' => '_visibility',
                        'compare' => 'NOT EXISTS'
                    ]
                ]
            ];

            $query->set('meta_query', $meta_query);

            // Увеличиваем количество результатов на странице
            $query->set('posts_per_page', 12);

            // Улучшаем релевантность для продуктов
            $query->set('orderby', 'relevance');
            $query->set('order', 'DESC');

            // Группируем по ID для избежания дублей
            add_filter('posts_groupby', [self::class, 'searchGroupBy']);
            add_filter('posts_distinct', [self::class, 'searchDistinct']);

            // Добавляем поиск по мета-полям только если не добавлен ранее
            if (!has_filter('posts_search', [self::class, 'includeProductMetaInSearch'])) {
                add_filter('posts_search', [self::class, 'includeProductMetaInSearch'], 500, 2);
            }
        }
    }

    /**
     * Включает поиск по мета-полям продуктов (ACF поля, исключая SKU)
     */
    public static function includeProductMetaInSearch($search, $wp_query)
    {
        // Проверяем что это поисковый запрос и избегаем рекурсии
        if (!$wp_query->is_search() || empty($search)) {
            return $search;
        }

        global $wpdb;

        $search_term = $wp_query->get('s');
        if (empty($search_term)) {
            return $search;
        }

        // Убираем фильтр чтобы избежать бесконечной рекурсии
        remove_filter('posts_search', [self::class, 'includeProductMetaInSearch'], 500);

        // Экранируем поисковый термин
        $escaped_term = '%' . $wpdb->esc_like($search_term) . '%';

        // Поиск в мета-полях продуктов (убрали SKU)
        $meta_search = $wpdb->prepare(
            "
            OR EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm
                WHERE pm.post_id = {$wpdb->posts}.ID
                AND (
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key = %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s) OR
                    (pm.meta_key LIKE %s AND pm.meta_value LIKE %s)
                )
            )
        ",
            'companies_%',
            $escaped_term,
            'sma_%',
            $escaped_term,
            'newsletter_%',
            $escaped_term,
            'landing_page_%',
            $escaped_term,
            'product_type',
            $escaped_term,
            '%_target',
            $escaped_term,
            '%_label',
            $escaped_term
        );

        // Поиск по таксономиям продуктов
        $taxonomy_search = $wpdb->prepare("
            OR EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} tr
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                WHERE tr.object_id = {$wpdb->posts}.ID
                AND tt.taxonomy IN ('product_cat', 'product_tag', 'product_tags_hierarchy')
                AND t.name LIKE %s
            )
        ", $escaped_term);

        // Модифицируем поисковый запрос более безопасно
        if (preg_match('/\(\(\(.*?\)\)\)/', $search)) {
            $search = preg_replace(
                '/(\(\(\(.*?\)\)\))/',
                '$1' . $meta_search . $taxonomy_search,
                $search
            );
        } else {
            // Fallback если структура запроса отличается
            $search .= $meta_search . $taxonomy_search;
        }

        return $search;
    }

    /**
     * AJAX функция поиска только для продуктов WooCommerce с улучшенной фильтрацией
     */
    public static function ajaxSearchProducts()
    {
        // Логируем запрос для отладки
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AJAX Search Request: ' . print_r($_REQUEST, true));
        }

        // Проверяем nonce (необязательно для публичного поиска)
        $nonce = sanitize_text_field($_REQUEST['nonce'] ?? '');
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'search_nonce')) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Search nonce failed, but continuing...');
            }
        }

        $search_term = sanitize_text_field($_REQUEST['s'] ?? '');
        $per_page = max(1, min(20, intval($_REQUEST['per_page'] ?? 6)));

        // Логируем параметры поиска
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Search term: '{$search_term}', per_page: {$per_page}");
        }

        if (strlen($search_term) < 2) {
            wp_send_json_success([]);
            return;
        }

        try {
            $results = [];
            $seen_ids = [];

            // Поиск только продуктов WooCommerce
            if (function_exists('wc_get_products')) {
                $products = wc_get_products([
                    'limit' => $per_page * 5, // Берем больше для фильтрации по релевантности
                    'status' => 'publish',
                    'search' => $search_term,
                    'orderby' => 'relevance',
                    'order' => 'DESC',
                    'type' => ['simple', 'variable'],
                    'visibility' => 'visible',
                ]);

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WC Products found: ' . count($products));
                }

                // Массив для расчета релевантности
                $products_with_scores = [];

                foreach ($products as $product) {
                    // Проверяем видимость продукта
                    if (!$product->is_visible()) {
                        continue;
                    }

                    // Исключаем вариации продуктов
                    if ($product->get_type() === 'variation') {
                        continue;
                    }

                    // Проверяем статус продукта
                    if ($product->get_status() !== 'publish') {
                        continue;
                    }

                    // Вычисляем релевантность
                    $relevance_score = self::calculateAjaxProductRelevance($product, $search_term);

                    // Устанавливаем минимальный порог релевантности
                    if ($relevance_score < 20) {
                        continue; // Пропускаем товары с низкой релевантностью
                    }

                    $products_with_scores[] = [
                        'product' => $product,
                        'score' => $relevance_score
                    ];
                }

                // Сортируем по релевантности
                usort($products_with_scores, function ($a, $b) {
                    return $b['score'] - $a['score'];
                });


                // Если есть товары с очень высокой релевантностью (50+), показываем только высокорелевантные
                $has_high_relevant = false;
                foreach ($products_with_scores as $item) {
                    if ($item['score'] >= 50) {
                        $has_high_relevant = true;
                        break;
                    }
                }

                if ($has_high_relevant) {
                    // Фильтруем только товары с релевантностью 25+
                    $products_with_scores = array_filter($products_with_scores, function ($item) {
                        return $item['score'] >= 25;
                    });
                }

                // Берем только нужное количество самых релевантных товаров
                $top_products = array_slice($products_with_scores, 0, min($per_page, 5));

                // Берем только нужное количество самых релевантных товаров
                $top_products = array_slice($products_with_scores, 0, $per_page);

                foreach ($top_products as $item) {
                    $product = $item['product'];

                    if (in_array($product->get_id(), $seen_ids)) {
                        continue;
                    }

                    $seen_ids[] = $product->get_id();

                    $image_id = $product->get_image_id();
                    $image_url = null;

                    if ($image_id) {
                        $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                        $image_url = $image ? $image[0] : null;
                    }

                    $excerpt = $product->get_short_description();
                    if (empty($excerpt)) {
                        $excerpt = wp_trim_words($product->get_description(), 15);
                    }
                    if (empty($excerpt)) {
                        $excerpt = 'Product description not available';
                    }

                    // Очищаем excerpt от HTML тегов
                    $excerpt = wp_strip_all_tags($excerpt);
                    $excerpt = wp_trim_words($excerpt, 20);

                    $results[] = [
                        'id' => $product->get_id(),
                        'title' => $product->get_name(),
                        'url' => $product->get_permalink(),
                        'excerpt' => $excerpt,
                        'type' => 'Product',
                        'image' => $image_url,
                        'price' => $product->get_price_html(),
                        'in_stock' => $product->is_in_stock(),
                        'relevance_score' => $item['score'] // Для отладки
                    ];
                }

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Filtered relevant products: ' . count($results));
                    foreach ($results as $result) {
                        error_log("Product: {$result['title']}, Score: {$result['relevance_score']}");
                    }
                }
            } else {
                // Если WooCommerce не активен, возвращаем пустой результат
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WooCommerce not found, no products to search');
                }
                wp_send_json_success([]);
                return;
            }

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Total relevant product results: ' . count($results));
            }

            wp_send_json_success($results);
        } catch (Exception $e) {
            error_log('AJAX Search Error: ' . $e->getMessage());
            wp_send_json_error([
                'message' => 'Product search failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Новая функция расчета релевантности для AJAX поиска
     */
    private static function calculateAjaxProductRelevance($product, $search_term)
    {
        if (!is_object($product) || empty($search_term)) {
            return 0;
        }

        $score = 0;
        $search_term_lower = strtolower(trim($search_term));

        // Проверяем заголовок продукта (самый высокий приоритет)
        $title_lower = strtolower($product->get_name());
        if (!empty($title_lower)) {
            // Точное совпадение названия
            if ($title_lower === $search_term_lower) {
                $score += 100;
            }
            // Название начинается с поискового запроса
            else if (strpos($title_lower, $search_term_lower) === 0) {
                $score += 50;
            }
            // Поисковый запрос содержится в названии
            else if (strpos($title_lower, $search_term_lower) !== false) {
                $score += 25;
            }
            // Частичное совпадение слов
            else {
                $search_words = explode(' ', $search_term_lower);
                $title_words = explode(' ', $title_lower);

                foreach ($search_words as $search_word) {
                    if (strlen($search_word) < 3) continue; // Игнорируем короткие слова

                    foreach ($title_words as $title_word) {
                        if (strpos($title_word, $search_word) !== false) {
                            $score += 10;
                        }
                    }
                }
            }
        }

        // Проверяем описание продукта (средний приоритет)
        $description = $product->get_short_description();
        if (empty($description)) {
            $description = $product->get_description();
        }

        if (!empty($description)) {
            $description_lower = strtolower(wp_strip_all_tags($description));
            if (strpos($description_lower, $search_term_lower) !== false) {
                $score += 10;
            }
        }

        // Проверяем категории продукта
        $categories = wp_get_post_terms($product->get_id(), 'product_cat');
        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                if (strpos(strtolower($category->name), $search_term_lower) !== false) {
                    $score += 15;
                }
            }
        }

        // Проверяем теги продукта
        $tags = wp_get_post_terms($product->get_id(), 'product_tag');
        if (!is_wp_error($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if (strpos(strtolower($tag->name), $search_term_lower) !== false) {
                    $score += 10;
                }
            }
        }

        // Бонус для товаров в наличии
        if ($product->is_in_stock()) {
            $score += 5;
        }

        // Бонус для товаров с изображениями
        if ($product->get_image_id()) {
            $score += 2;
        }

        return max(0, $score);
    }

    /**
     * Добавление переменных для AJAX поиска
     */
    public static function addSearchAjaxVars()
    {
        if (is_admin()) {
            return;
        }

        // Создаем скрипт в footer
        add_action('wp_footer', function () {
?>
            <script>
                // Убеждаемся что переменные доступны
                if (!window.searchAjax) {
                    window.searchAjax = {
                        ajax_url: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
                        nonce: '<?php echo esc_js(wp_create_nonce('search_nonce')); ?>',
                        home_url: '<?php echo esc_js(home_url()); ?>'
                    };
                    console.log('Search Ajax vars initialized:', window.searchAjax);
                }

                // Также добавляем стандартную переменную WordPress
                if (!window.ajaxurl) {
                    window.ajaxurl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
                }
            </script>
        <?php
        }, 5);
    }

    /**
     * Улучшает релевантность результатов поиска продуктов
     */
    public static function improveSearchRelevance($posts, $query)
    {
        if (!$query->is_search() || empty($posts) || !is_array($posts)) {
            return $posts;
        }

        $search_term = $query->get('s');
        if (empty($search_term)) {
            return $posts;
        }

        // Ограничиваем сортировку для больших результатов
        if (count($posts) > 50) {
            return $posts;
        }

        try {
            // Сортируем результаты по релевантности для продуктов
            usort($posts, function ($a, $b) use ($search_term) {
                $score_a = self::calculateProductRelevanceScore($a, $search_term);
                $score_b = self::calculateProductRelevanceScore($b, $search_term);
                return $score_b - $score_a; // Сортировка по убыванию
            });
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Product search relevance sorting error: ' . $e->getMessage());
            }
        }

        return $posts;
    }

    /**
     * Вычисляет оценку релевантности для продукта (убрали SKU)
     */
    private static function calculateProductRelevanceScore($post, $search_term)
    {
        if (!is_object($post) || empty($search_term) || $post->post_type !== 'product') {
            return 0;
        }

        $score = 0;
        $search_term_lower = strtolower(trim($search_term));

        // Проверяем заголовок продукта (высокий приоритет)
        $title_lower = strtolower($post->post_title ?? '');
        if (!empty($title_lower) && strpos($title_lower, $search_term_lower) !== false) {
            $score += 15;
            // Бонус если точное совпадение
            if ($title_lower === $search_term_lower) {
                $score += 25;
            }
            // Бонус если поиск в начале названия
            if (strpos($title_lower, $search_term_lower) === 0) {
                $score += 10;
            }
        }

        // Проверяем описание продукта (средний приоритет)
        $content_lower = strtolower($post->post_content ?? '');
        if (!empty($content_lower)) {
            $matches = substr_count($content_lower, $search_term_lower);
            $score += min($matches * 3, 12); // Ограничиваем максимальный бонус
        }

        // Проверяем категории продукта
        $categories = get_the_terms($post->ID, 'product_cat');
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                if (strpos(strtolower($category->name), $search_term_lower) !== false) {
                    $score += 5;
                }
            }
        }

        // Бонус для продуктов в наличии
        $in_stock = get_post_meta($post->ID, '_stock_status', true);
        if ($in_stock === 'instock') {
            $score += 2;
        }

        return max(0, $score);
    }

    /**
     * Улучшенное кеширование результатов поиска с фильтрацией релевантности
     */
    public static function cacheSearchResults($posts, $query)
    {
        if ($query->is_search() && !is_admin()) {
            $search_query = $query->get('s');

            // Создаем более детальный ключ кеша
            $cache_data = [
                'search_term' => $search_query,
                'post_type' => $query->get('post_type'),
                'posts_per_page' => $query->get('posts_per_page'),
                'meta_query' => $query->get('meta_query'),
                'timestamp' => time()
            ];

            $cache_key = 'product_search_' . md5(serialize($cache_data));

            // Фильтруем результаты по релевантности перед кешированием
            if (!empty($posts) && is_array($posts)) {
                $filtered_posts = [];

                foreach ($posts as $post) {
                    if ($post->post_type === 'product') {
                        $product = wc_get_product($post->ID);
                        if ($product) {
                            $relevance = self::calculateAjaxProductRelevance($product, $search_query);

                            // Сохраняем только релевантные результаты
                            if ($relevance >= 5) {
                                $post->search_relevance = $relevance;
                                $filtered_posts[] = $post;
                            }
                        }
                    }
                }

                // Сортируем по релевантности
                usort($filtered_posts, function ($a, $b) {
                    $score_a = isset($a->search_relevance) ? $a->search_relevance : 0;
                    $score_b = isset($b->search_relevance) ? $b->search_relevance : 0;
                    return $score_b - $score_a;
                });

                $posts = $filtered_posts;
            }

            // Кешируем на более короткий период для более актуальных результатов
            wp_cache_set($cache_key, $posts, 'product_search_results', 30 * MINUTE_IN_SECONDS);
        }

        return $posts;
    }

    /**
     * Восстановление из кеша
     */
    public static function getCachedSearchResults($posts, $query)
    {
        if ($query->is_search() && !is_admin()) {
            $search_query = $query->get('s');

            $cache_data = [
                'search_term' => $search_query,
                'post_type' => $query->get('post_type'),
                'posts_per_page' => $query->get('posts_per_page'),
                'meta_query' => $query->get('meta_query'),
                'timestamp' => time()
            ];

            $cache_key = 'product_search_' . md5(serialize($cache_data));
            $cached_posts = wp_cache_get($cache_key, 'product_search_results');
            if ($cached_posts !== false) {
                return $cached_posts;
            }
        }
        return $posts;
    }

    /**
     * Очистка кеша поиска при изменении продуктов
     */
    public static function clearSearchCache($post_id)
    {
        // Получаем тип поста
        $post_type = get_post_type($post_id);

        // Очищаем кеш только при изменении продуктов
        if ($post_type === 'product') {
            wp_cache_flush_group('product_search_results');

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Search cache cleared due to product update: {$post_id}");
            }
        }
    }

    /**
     * Очистка кеша при изменении терминов продуктов
     */
    public static function clearSearchCacheOnTermUpdate($term_id, $tt_id, $taxonomy)
    {
        // Очищаем кеш при изменении категорий и тегов продуктов
        if (in_array($taxonomy, ['product_cat', 'product_tag'])) {
            wp_cache_flush_group('product_search_results');

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Search cache cleared due to taxonomy update: {$taxonomy}");
            }
        }
    }

    /**
     * Логирование поисковых запросов
     */
    public static function logSearchQuery()
    {
        if (!is_search() || is_admin()) {
            return;
        }

        global $wp_query;

        if (!$wp_query || !$wp_query->is_search()) {
            return;
        }

        $search_term = get_search_query();

        if (empty($search_term) || strlen($search_term) < 2 || strlen($search_term) > 100) {
            return;
        }

        $search_term = sanitize_text_field($search_term);

        try {
            $option_key = 'popular_product_searches';
            $searches = get_option($option_key, []);

            if (!is_array($searches)) {
                $searches = [];
            }

            if (isset($searches[$search_term])) {
                $searches[$search_term]++;
            } else {
                $searches[$search_term] = 1;
            }

            if (count($searches) > 100) {
                arsort($searches);
                $searches = array_slice($searches, 0, 50, true);
            }

            update_option($option_key, $searches);
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Product search logging error: ' . $e->getMessage());
            }
        }
    }

    /**
     * SEO мета-теги для поиска продуктов
     */
    public static function searchSeoMeta()
    {
        if (is_search()) {
            $search_query = get_search_query();
            global $wp_query;
            $total_results = $wp_query->found_posts ?? 0;

            echo '<meta name="robots" content="noindex,follow">' . "\n";
            echo '<meta name="description" content="Product search results for \'' . esc_attr($search_query) . '\'. Found ' . $total_results . ' products.">' . "\n";
            echo '<link rel="canonical" href="' . esc_url(get_search_link($search_query)) . '">' . "\n";
        }
    }

    /**
     * Schema.org разметка для поиска продуктов
     */
    public static function addSearchSchema()
    {
        if (is_search()) {
            $search_query = get_search_query();
            global $wp_query;
            $total_results = $wp_query->found_posts ?? 0;
        ?>
            <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "SearchResultsPage",
                    "name": "Product Search Results for '<?php echo esc_js($search_query); ?>'",
                    "url": "<?php echo esc_url(get_search_link($search_query)); ?>",
                    "numberOfItems": <?php echo intval($total_results); ?>,
                    "mainEntity": {
                        "@type": "ItemList",
                        "numberOfItems": <?php echo intval($total_results); ?>,
                        "itemListElement": "Product"
                    },
                    "potentialAction": {
                        "@type": "SearchAction",
                        "target": "<?php echo esc_url(home_url('/shop/?s={search_term_string}')); ?>",
                        "query-input": "required name=search_term_string"
                    }
                }
            </script>
        <?php
        }
    }

    /**
     * Отладочная информация
     */
    public static function addSearchDebugInfo()
    {
        if (!is_admin() && (is_search() || is_front_page())) {
        ?>
            <script>
                console.log('Product Search Debug Info:', {
                    ajax_url: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
                    home_url: '<?php echo esc_js(home_url()); ?>',
                    shop_url: '<?php echo esc_js(wc_get_page_permalink('shop')); ?>',
                    nonce: '<?php echo esc_js(wp_create_nonce('search_nonce')); ?>',
                    is_woocommerce: <?php echo function_exists('wc_get_products') ? 'true' : 'false'; ?>,
                    products_count: <?php echo function_exists('wc_get_products') ? count(wc_get_products(['limit' => 1])) : 0; ?>
                });
            </script>
<?php
        }
    }

    /**
     * Отладка поискового запроса
     */
    public static function debugSearchQuery($query)
    {
        if (!is_admin() && $query->is_search()) {
            error_log('Product Search Query SQL: ' . $query->request);
            error_log('Search Term: ' . $query->get('s'));
            error_log('Post Types: ' . print_r($query->get('post_type'), true));
        }
    }

    /**
     * Тестирование функций поиска продуктов
     */
    public static function testSearchFunctionality()
    {
        if (isset($_GET['test_product_search']) && $_GET['test_product_search'] == '1') {
            echo '<h2>Тест функции поиска продуктов</h2>';

            if (function_exists('wc_get_products')) {
                echo '<h3>WooCommerce продукты:</h3>';
                $products = wc_get_products(['limit' => 5, 'status' => 'publish']);

                foreach ($products as $product) {
                    echo '<div>';
                    echo '<strong>' . $product->get_name() . '</strong><br>';
                    echo 'ID: ' . $product->get_id() . '<br>';
                    echo 'Type: ' . $product->get_type() . '<br>';
                    echo 'Visible: ' . ($product->is_visible() ? 'Yes' : 'No') . '<br>';
                    echo 'In Stock: ' . ($product->is_in_stock() ? 'Yes' : 'No') . '<br>';
                    echo 'URL: ' . $product->get_permalink() . '<br>';
                    echo '</div><hr>';
                }

                // Тест релевантности
                echo '<h3>Тест релевантности для поиска "test":</h3>';
                $test_products = wc_get_products(['limit' => 10, 'search' => 'test']);
                foreach ($test_products as $product) {
                    $score = self::calculateAjaxProductRelevance($product, 'test');
                    echo '<div>';
                    echo '<strong>' . $product->get_name() . '</strong> - Score: ' . $score . '<br>';
                    echo '</div>';
                }
            } else {
                echo '<p>WooCommerce не активен</p>';
            }
            exit;
        }
    }

    /**
     * Получает популярные поиски продуктов
     */
    public static function getPopularProductSearches($limit = 10)
    {
        $limit = max(1, min(50, intval($limit)));
        $searches = get_option('popular_product_searches', []);

        if (!is_array($searches)) {
            return [];
        }

        arsort($searches);
        return array_slice(array_keys($searches), 0, $limit);
    }

    /**
     * Получает статистику поисков
     */
    public static function getSearchStatistics()
    {
        $searches = get_option('popular_product_searches', []);

        if (!is_array($searches) || empty($searches)) {
            return [
                'total_searches' => 0,
                'unique_terms' => 0,
                'most_popular' => null,
                'average_per_term' => 0
            ];
        }

        $total_searches = array_sum($searches);
        $unique_terms = count($searches);
        arsort($searches);
        $most_popular = array_key_first($searches);
        $average_per_term = $total_searches / $unique_terms;

        return [
            'total_searches' => $total_searches,
            'unique_terms' => $unique_terms,
            'most_popular' => $most_popular,
            'average_per_term' => round($average_per_term, 2)
        ];
    }

    /**
     * Группировка результатов поиска для избежания дублей
     */
    public static function searchGroupBy($groupby)
    {
        global $wpdb;

        if (is_search()) {
            $groupby = "{$wpdb->posts}.ID";
        }

        return $groupby;
    }

    /**
     * Обеспечение уникальности результатов поиска
     */
    public static function searchDistinct($distinct)
    {
        if (is_search()) {
            return "DISTINCT";
        }

        return $distinct;
    }

    /**
     * Удаление фильтров поиска после выполнения запроса
     */
    public static function removeSearchFilters()
    {
        remove_filter('posts_groupby', [self::class, 'searchGroupBy']);
        remove_filter('posts_distinct', [self::class, 'searchDistinct']);
    }

    /**
     * Очистка всего кеша поиска (административная функция)
     */
    public static function clearAllSearchCache()
    {
        wp_cache_flush_group('product_search_results');

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('All search cache cleared manually');
        }

        return true;
    }

    /**
     * Получить информацию о кеше поиска
     */
    public static function getCacheInfo()
    {
        // Примерная информация о кеше (WordPress Object Cache не предоставляет детальную статистику)
        return [
            'cache_group' => 'product_search_results',
            'cache_timeout' => '30 minutes',
            'last_cleared' => get_option('search_cache_last_cleared', 'Never')
        ];
    }

    /**
     * Валидация поискового запроса
     */
    public static function validateSearchQuery($query)
    {
        $query = trim($query);

        // Минимальная длина
        if (strlen($query) < 2) {
            return false;
        }

        // Максимальная длина
        if (strlen($query) > 100) {
            return false;
        }

        // Запрещенные символы
        $forbidden_chars = ['<', '>', '"', "'", '\\', '/', ';'];
        foreach ($forbidden_chars as $char) {
            if (strpos($query, $char) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Санитизация поискового запроса
     */
    public static function sanitizeSearchQuery($query)
    {
        // Убираем лишние пробелы
        $query = trim($query);

        // Убираем множественные пробелы
        $query = preg_replace('/\s+/', ' ', $query);

        // Убираем потенциально опасные символы
        $query = sanitize_text_field($query);

        // Ограничиваем длину
        if (strlen($query) > 100) {
            $query = substr($query, 0, 100);
        }

        return $query;
    }

    /**
     * Проверка, является ли запрос спамом или атакой
     */
    public static function isSpamQuery($query)
    {
        $query_lower = strtolower($query);

        // Проверяем на SQL инъекции
        $sql_patterns = [
            'union select',
            'drop table',
            'insert into',
            'delete from',
            'update set',
            '--',
            '/*',
            '*/',
            'script',
            'javascript:'
        ];

        foreach ($sql_patterns as $pattern) {
            if (strpos($query_lower, $pattern) !== false) {
                return true;
            }
        }

        // Проверяем на повторяющиеся символы (возможный спам)
        if (preg_match('/(.)\1{10,}/', $query)) {
            return true;
        }

        // Проверяем на слишком много специальных символов
        $special_char_count = preg_match_all('/[^a-zA-Zа-яА-Я0-9\s]/', $query);
        if ($special_char_count > strlen($query) / 2) {
            return true;
        }

        return false;
    }
}
