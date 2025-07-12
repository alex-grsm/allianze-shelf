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
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets', 'newsletter', 'landing_page']),
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
                'maxlength' => 500,
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets', 'newsletter', 'landing_page'], 'field_buyout_enabled', '1'),
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
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets', 'newsletter', 'landing_page'], 'field_buyout_enabled', '1'),
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

        return [
            // Buyout Details
            'buyout_enabled' => self::isBuyoutEnabled($product),
            'buyout_description' => self::getBuyoutDescription($product),
            'buyout_table_image' => self::getBuyoutTableImage($product),
            'has_buyout_content' => self::hasBuyoutContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return [
            'buyoutDetails' => self::getBuyoutDetailsForTemplate($product),
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return [
            'buyoutDetails' => null,
        ];
    }

    // ===== PRIVATE METHODS =====

    /**
     * Check if buyout details are enabled
     */
    private static function isBuyoutEnabled(WC_Product $product): bool
    {
        return (bool) get_field('buyout_enabled', $product->get_id());
    }

    /**
     * Get buyout description
     */
    private static function getBuyoutDescription(WC_Product $product): string
    {
        return get_field('buyout_description', $product->get_id()) ?: '';
    }

    /**
     * Get buyout table image
     */
    private static function getBuyoutTableImage(WC_Product $product): ?array
    {
        return get_field('buyout_table_image', $product->get_id());
    }

    /**
     * Check if product has buyout content
     */
    private static function hasBuyoutContent(WC_Product $product): bool
    {
        if (!self::isBuyoutEnabled($product)) {
            return false;
        }

        $description = self::getBuyoutDescription($product);
        $image = self::getBuyoutTableImage($product);

        return !empty($description) || !empty($image);
    }

    /**
     * Get buyout details formatted for template (compatibility with existing buyout-details.blade.php)
     */
    private static function getBuyoutDetailsForTemplate(WC_Product $product): ?array
    {
        // Проверяем, включены ли buyout детали и тип продукта
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets', 'newsletter', 'landing_page']) || !self::isBuyoutEnabled($product)) {
            return null;
        }

        $description = self::getBuyoutDescription($product);
        $image = self::getBuyoutTableImage($product);

        // Если нет контента, возвращаем null
        if (empty($description) && empty($image)) {
            return null;
        }

        return [
            'description' => $description ?: '',
            'table_image' => $image,
            'table_alt' => $image['alt'] ?? 'Buyout details table',
            'has_image' => !empty($image),
            'has_content' => !empty($description) || !empty($image),
        ];
    }
}
