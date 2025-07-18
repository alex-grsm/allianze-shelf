<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class BuyoutDetailsTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Buyout Details
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: BUYOUT DETAILS =====
            [
                'key' => 'field_buyout_tab',
                'label' => 'Buyout Details',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
            ],
            [
                'key' => 'field_buyout_enabled',
                'label' => 'Enable Buyout Details',
                'name' => 'buyout_enabled',
                'type' => 'true_false',
                'instructions' => 'Enable to show buyout details section on product page.',
                'required' => 0,
                'message' => 'Show buyout details on product page',
                'default_value' => 1,
                'ui' => 1,
                'ui_on_text' => 'Yes',
                'ui_off_text' => 'No',
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
            ],
            [
                'key' => 'field_buyout_description',
                'label' => 'Buyout Description',
                'name' => 'buyout_description',
                'type' => 'textarea',
                'instructions' => 'Enter detailed description for buyout terms and conditions.',
                'required' => 0,
                'rows' => 4,
                'placeholder' => 'Enter buyout details description...',
                'maxlength' => 3000,
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
            ],
            [
                'key' => 'field_buyout_table_image',
                'label' => 'Buyout Table (Image)',
                'name' => 'buyout_table_image',
                'type' => 'image',
                'instructions' => 'Upload table with buyout details.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'mime_types' => 'png,jpg,jpeg',
                'conditional_logic' => create_acf_conditional_logic(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
            'buyout_enabled' => self::getBooleanFieldValue('buyout_enabled', $productId, true),
            'buyout_description' => self::getFieldValue('buyout_description', $productId),
            'buyout_table_image' => self::getBuyoutTableImage($product),
            'has_buyout_content' => self::hasBuyoutContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter', 'landing_page'])
            || !self::isBuyoutEnabled($product)) {
            return ['buyoutDetails' => null];
        }

        $description = self::getFieldValue('buyout_description', $product->get_id());
        $imageData = self::getBuyoutTableImage($product);

        // Если нет контента, возвращаем null
        if (empty($description) && empty($imageData)) {
            return ['buyoutDetails' => null];
        }

        return [
            'buyoutDetails' => [
                'description' => $description,
                'table_image' => $imageData ? $imageData['original'] : null, // Для совместимости
                'table_image_data' => $imageData, // Новые форматированные данные
                'table_alt' => $imageData ? ($imageData['alt'] ?: 'Buyout details table') : '',
                'has_image' => !empty($imageData),
                'has_content' => !empty($description) || !empty($imageData),
            ]
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return ['buyoutDetails' => null];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Проверить, включены ли buyout details
     */
    private static function isBuyoutEnabled(WC_Product $product): bool
    {
        return self::getBooleanFieldValue('buyout_enabled', $product->get_id(), true);
    }

    /**
     * Получить изображение таблицы buyout с форматированием
     */
    private static function getBuyoutTableImage(WC_Product $product): ?array
    {
        $imageField = get_field('buyout_table_image', $product->get_id());

        if (!$imageField) {
            return null;
        }

        $formattedData = self::formatImageData($imageField);

        if ($formattedData) {
            // Добавляем оригинальные данные для совместимости
            $formattedData['original'] = $imageField;
        }

        return $formattedData;
    }

    /**
     * Проверить, есть ли контент для buyout
     */
    private static function hasBuyoutContent(WC_Product $product): bool
    {
        if (!self::isBuyoutEnabled($product)) {
            return false;
        }

        $productId = $product->get_id();
        $description = self::getFieldValue('buyout_description', $productId);
        $image = self::getBuyoutTableImage($product);

        return !empty($description) || !empty($image);
    }

    /**
     * Проверить, есть ли данные для отображения
     */
    protected static function hasContent(WC_Product $product): bool
    {
        return self::hasBuyoutContent($product);
    }
}
