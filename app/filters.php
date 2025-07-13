<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Очистка <head> от ненужных тегов для улучшения производительности
 */
add_action('init', function () {
    // Удаляем ненужные мета-теги
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    remove_action('wp_head', 'feed_links_extra', 3);

    // Удаляем лишние RSS ссылки (оставляем основные)
    remove_action('wp_head', 'feed_links', 2);
    add_action('wp_head', 'feed_links', 3); // Перемещаем в конец
});

/**
 * Отключение XML-RPC для безопасности
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Удаление версии из статических файлов (безопасность)
 */
add_filter('style_loader_src', function ($src) {
    if (strpos($src, 'ver=' . get_bloginfo('version'))) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
});

add_filter('script_loader_src', function ($src) {
    if (strpos($src, 'ver=' . get_bloginfo('version'))) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
});

/**
 * Ограничение REST API (безопасная версия для WooCommerce)
 */
add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
        return $result;
    }

    // Разрешаем WooCommerce Store API для AJAX корзины
    if (strpos($_SERVER['REQUEST_URI'], '/wp-json/wc/store/') !== false) {
        return $result;
    }

    // Разрешаем стандартные WooCommerce endpoints
    if (strpos($_SERVER['REQUEST_URI'], '/wp-json/wc/') !== false) {
        return $result;
    }

    if (!is_user_logged_in()) {
        return new \WP_Error('rest_not_logged_in', 'You are not currently logged in.', ['status' => 401]);
    }

    return $result;
});

/**
 * Оптимизация WooCommerce стилей (безопасная версия)
 */
add_action('wp_enqueue_scripts', function () {
    if (function_exists('is_woocommerce')) {
        $needs_woo_scripts = is_woocommerce() || is_cart() || is_checkout() || is_account_page();

        if (!$needs_woo_scripts) {
            // Удаляем только стили (скрипты оставляем для AJAX корзины)
            wp_dequeue_style('woocommerce-general');
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
        }
    }
}, 99);

/**
 * Отключение Gutenberg блоков CSS (безопасная версия)
 */
add_action('wp_enqueue_scripts', function () {
    // Не отключаем на страницах где могут быть WooCommerce блоки
    if (!is_cart() && !is_checkout() && !is_account_page()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-blocks-style');
    }
});

/**
 * Оптимизация запросов - ограничение revisions
 */
add_filter('wp_revisions_to_keep', function ($num, $post) {
    return 5; // Оставляем только 5 последних ревизий
}, 10, 2);

/**
 * Удаление ненужных dashboard виджетов в админке
 */
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    // Оставляем dashboard_right_now и dashboard_activity - они полезны
});
