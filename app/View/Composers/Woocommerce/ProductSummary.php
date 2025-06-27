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
                // 'price' => $product->get_price_html(),
                'short_description' => $product->get_short_description(),
                'description' => $product->get_description(),
                'type' => $product->get_type(),
                'variations' => $this->getVariationsData($product),
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
        if ($variation instanceof WC_Product_Variation) {
            $attributes = $variation->get_variation_attributes();

            foreach ($attributes as $key => $value) {
                // Простое форматирование названия
                $name = str_replace(['attribute_', 'pa_', '_', '-'], ['', '', ' ', ' '], $key);
                $name = ucwords($name);

                $formatted_attributes[$name] = $value;
            }
        }

        $variations_data[] = [
            'id' => $variation->get_id(),
            'regular_price' => $variation->get_regular_price(),
            'attributes' => $formatted_attributes,
            // 'price_html' => $variation->get_price_html(),
            // 'sale_price' => $variation->get_sale_price(),
            // 'is_downloadable' => $variation->is_downloadable(),
            // 'is_virtual' => $variation->is_virtual(),
            // 'is_in_stock' => $variation->is_in_stock(),
            // 'sku' => $variation->get_sku(),
        ];
    }

    return $variations_data;
}


}
