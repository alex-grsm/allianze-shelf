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
                'image' => $this->getProductImage($product),
                'gallery' => $this->getProductGallery($product),
            ]
        ];
    }

    /**
     * Получить основное изображение товара
     */
    private function getProductImage(WC_Product $product): ?array
    {
        $image_id = $product->get_image_id();

        if (!$image_id) {
            return null;
        }

        return $this->formatImageData($image_id);
    }

    /**
     * Получить галерею изображений товара
     */
    private function getProductGallery(WC_Product $product): array
    {
        $gallery_ids = $product->get_gallery_image_ids();
        $gallery = [];

        foreach ($gallery_ids as $image_id) {
            $image_data = $this->formatImageData($image_id);
            if ($image_data) {
                $gallery[] = $image_data;
            }
        }

        return $gallery;
    }

    /**
     * Форматировать данные изображения
     */
    private function formatImageData(int $image_id): ?array
    {
        if (!$image_id) {
            return null;
        }

        // Получаем разные размеры изображения
        $full = wp_get_attachment_image_src($image_id, 'full');
        $large = wp_get_attachment_image_src($image_id, 'large');
        $medium = wp_get_attachment_image_src($image_id, 'medium');
        $thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');

        // Получаем размеры WooCommerce
        $woocommerce_single = wp_get_attachment_image_src($image_id, 'woocommerce_single');
        $woocommerce_thumbnail = wp_get_attachment_image_src($image_id, 'woocommerce_thumbnail');
        $woocommerce_gallery_thumbnail = wp_get_attachment_image_src($image_id, 'woocommerce_gallery_thumbnail');

        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        return [
            'id' => $image_id,
            'alt' => $alt_text ?: '',
            'caption' => wp_get_attachment_caption($image_id),
            'full' => $full ? [
                'url' => $full[0],
                'width' => $full[1],
                'height' => $full[2]
            ] : null,
            'large' => $large ? [
                'url' => $large[0],
                'width' => $large[1],
                'height' => $large[2]
            ] : null,
            'medium' => $medium ? [
                'url' => $medium[0],
                'width' => $medium[1],
                'height' => $medium[2]
            ] : null,
            'thumbnail' => $thumbnail ? [
                'url' => $thumbnail[0],
                'width' => $thumbnail[1],
                'height' => $thumbnail[2]
            ] : null,
            'woocommerce_single' => $woocommerce_single ? [
                'url' => $woocommerce_single[0],
                'width' => $woocommerce_single[1],
                'height' => $woocommerce_single[2]
            ] : null,
            'woocommerce_thumbnail' => $woocommerce_thumbnail ? [
                'url' => $woocommerce_thumbnail[0],
                'width' => $woocommerce_thumbnail[1],
                'height' => $woocommerce_thumbnail[2]
            ] : null,
            'woocommerce_gallery_thumbnail' => $woocommerce_gallery_thumbnail ? [
                'url' => $woocommerce_gallery_thumbnail[0],
                'width' => $woocommerce_gallery_thumbnail[1],
                'height' => $woocommerce_gallery_thumbnail[2]
            ] : null,
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
                'image' => $this->getVariationImage($variation), // Изображение вариации
            ];
        }

        return $variations_data;
    }

    /**
     * Получить изображение вариации товара
     */
    private function getVariationImage(WC_Product_Variation $variation): ?array
    {
        $image_id = $variation->get_image_id();

        // Если у вариации нет собственного изображения, используем изображение родительского товара
        if (!$image_id) {
            $parent_product = wc_get_product($variation->get_parent_id());
            if ($parent_product) {
                $image_id = $parent_product->get_image_id();
            }
        }

        if (!$image_id) {
            return null;
        }

        return $this->formatImageData($image_id);
    }
}
