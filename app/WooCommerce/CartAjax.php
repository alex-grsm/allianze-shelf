<?php

namespace App\WooCommerce;

class CartAjax
{
    public static function register()
    {
        add_action('wp_ajax_woocommerce_ajax_add_to_cart', [self::class, 'handleAddToCart']);
        add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', [self::class, 'handleAddToCart']);

        // Добавляем новый handler для получения количества
        add_action('wp_ajax_get_cart_count', [self::class, 'getCartCount']);
        add_action('wp_ajax_nopriv_get_cart_count', [self::class, 'getCartCount']);
    }

    public static function handleAddToCart()
    {
        if (isset($_POST['_wpnonce']) && !wp_verify_nonce($_POST['_wpnonce'], 'add_to_cart')) {
            wp_send_json_error('Invalid nonce');
        }

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
        $attributes = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $attributes[$key] = sanitize_text_field($value);
            }
        }

        if (!$product_id) {
            wp_send_json_error('No product ID provided');
        }

        $added = WC()->cart->add_to_cart($product_id, 1, $variation_id, $attributes);

        if ($added) {
            wp_send_json_success([
                'message' => 'Product added',
                'cart' => WC()->cart->get_cart(),
                'cart_count' => WC()->cart->get_cart_contents_count() // Добавляем счетчик сразу
            ]);
        } else {
            wp_send_json_error('Could not add to cart');
        }
    }

    /**
     * Получение количества товаров в корзине
     */
    public static function getCartCount()
    {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(['message' => 'WooCommerce not available']);
        }

        wp_send_json_success([
            'count' => WC()->cart->get_cart_contents_count(),
            'total' => WC()->cart->get_cart_total()
        ]);
    }
}
