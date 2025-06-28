<?php

namespace App\View\Composers\Woocommerce;

use Roots\Acorn\View\Composer;
use WC_Product;
use WC_Product_Variation;

class ProductSummary extends Composer
{
    protected static $views = [
        'partials.woocommerce.product-summary',
    ];

    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['productSummary' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['productSummary' => null];
        }

        return [
            'productSummary' => [
                'id' => $product->get_id(),
                'title' => $product->get_name(),
                'price' => $product->get_price(),
                'price_html' => $product->get_price_html(),
                'short_description' => $product->get_short_description(),
                'description' => $product->get_description(),
                'type' => $product->get_type(),
                'variations' => $this->getVariationsData($product),
                'is_in_stock' => $product->is_in_stock(),
                'is_purchasable' => $product->is_purchasable(),
                'add_to_cart_url' => $product->add_to_cart_url(),
                'ajax_add_to_cart' => 'yes' === get_option('woocommerce_enable_ajax_add_to_cart'),
                'cart_url' => wc_get_cart_url(),
                'checkout_url' => wc_get_checkout_url(),
            ]
        ];
    }

    /**
     * Получить данные вариаций для продукта
     */
    private function getVariationsData(WC_Product $product): ?array
    {
        if ($product->get_type() !== 'variable') {
            return null;
        }

        $variations_data = [];
        $variation_ids = $product->get_children();

        foreach ($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);

            if (!$variation) {
                continue;
            }

            // Получаем атрибуты с читаемыми названиями
            $formatted_attributes = [];
            $raw_attributes = [];

            if ($variation instanceof WC_Product_Variation) {
                $attributes = $variation->get_variation_attributes();

                foreach ($attributes as $key => $value) {
                    // Сохраняем сырые атрибуты для формы
                    $raw_attributes[$key] = $value;

                    // Простое форматирование названия для отображения
                    $name = str_replace(['attribute_', 'pa_', '_', '-'], ['', '', ' ', ' '], $key);
                    $name = ucwords($name);

                    $formatted_attributes[$name] = $value;
                }
            }

            $variations_data[] = [
                'id' => $variation->get_id(),
                'regular_price' => $variation->get_regular_price(),
                'sale_price' => $variation->get_sale_price(),
                'price' => $variation->get_price(),
                'price_html' => $variation->get_price_html(),
                'attributes' => $formatted_attributes,
                'raw_attributes' => $raw_attributes, // Для использования в форме
                'is_in_stock' => $variation->is_in_stock(),
                'is_purchasable' => $variation->is_purchasable(),
                'sku' => $variation->get_sku(),
                'stock_quantity' => $variation->get_stock_quantity(),
                'max_qty' => $variation->get_max_purchase_quantity(),
                'min_qty' => $variation->get_min_purchase_quantity(),
            ];
        }

        return $variations_data;
    }
}
