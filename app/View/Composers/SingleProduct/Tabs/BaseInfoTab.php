<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

/**
 * Base class for product information tabs (Companies, Social Media, Newsletter, Landing Page)
 * Handles common fields like country, rights date, target, year, buyout, label
 */
abstract class BaseInfoTab extends BaseTab
{
    /**
     * Get the product type this tab handles
     */
    abstract protected static function getProductType(): string;

    /**
     * Get the tab display name
     */
    abstract protected static function getTabName(): string;

    /**
     * Get additional fields specific to this tab (optional override)
     */
    protected static function getAdditionalFields(): array
    {
        return [];
    }

    /**
     * Get additional data specific to this tab (optional override)
     */
    protected static function getAdditionalData(WC_Product $product): array
    {
        return [];
    }

    /**
     * Get fields ACF for this info tab
     */
    public static function getFields(): array
    {
        $productType = static::getProductType();
        $tabName = static::getTabName();

        // Get standard info fields using helper
        $fields = create_product_info_fields($productType, $tabName);

        // Add any additional fields specific to this tab
        $additionalFields = static::getAdditionalFields();
        if (!empty($additionalFields)) {
            $fields = array_merge($fields, $additionalFields);
        }

        return $fields;
    }

    /**
     * Get data for product
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        $productType = static::getProductType();

        if (!self::isEnabledForProductType($product, [$productType])) {
            return null;
        }

        // Get unified meta data using helper
        $metaData = get_product_meta_data($product);
        $prefix = get_product_field_prefix($productType);

        // Create data array with prefixed keys for backward compatibility
        $data = self::createPrefixedData($metaData, $prefix);

        // Add any additional data specific to this tab
        $additionalData = static::getAdditionalData($product);
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        return $data;
    }

    /**
     * Get template data (for backward compatibility)
     * Most info tabs don't need separate template data
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return null;
    }

    /**
     * Get empty template data
     */
    public static function getEmptyTemplateData(): ?array
    {
        return null;
    }

    /**
     * Create prefixed data array for backward compatibility
     *
     * @param array $metaData Unified meta data from helper
     * @param string $prefix Field prefix for this product type
     * @return array
     */
    private static function createPrefixedData(array $metaData, string $prefix): array
    {
        $data = [];
        $fieldsToPrefix = [
            'country_code',
            'country_flag_url',
            'rights_until_date',
            'rights_until_formatted',
            'target',
            'year',
            'buyout',
            'label'
        ];

        foreach ($metaData as $key => $value) {
            if (in_array($key, $fieldsToPrefix)) {
                $prefixedKey = $prefix ? $prefix . $key : $key;
                $data[$prefixedKey] = $value;
            }
        }

        return $data;
    }

    /**
     * Get available product types for info tabs
     *
     * @return array
     */
    protected static function getAvailableProductTypes(): array
    {
        return [
            PRODUCT_TYPE_COMPANIES,
            PRODUCT_TYPE_SOCIAL_MEDIA_ASSETS,
            PRODUCT_TYPE_NEWSLETTER,
            PRODUCT_TYPE_LANDING_PAGE,
        ];
    }

    /**
     * Validate product type
     *
     * @param string $productType
     * @return bool
     */
    protected static function isValidProductType(string $productType): bool
    {
        return in_array($productType, self::getAvailableProductTypes());
    }

    /**
     * Get prefix for current product type
     *
     * @return string
     */
    protected static function getPrefix(): string
    {
        return get_product_field_prefix(static::getProductType());
    }

    /**
     * Get product type label for display
     *
     * @return string
     */
    protected static function getProductTypeLabel(): string
    {
        $labels = get_product_type_labels();
        return $labels[static::getProductType()] ?? static::getProductType();
    }

    /**
     * Check if this tab has content for given product
     *
     * @param WC_Product $product
     * @return bool
     */
    protected static function hasContent(WC_Product $product): bool
    {
        // Info tabs always have basic content if product type matches
        return self::isEnabledForProductType($product, [static::getProductType()]);
    }

    /**
     * Get common field configuration for info fields
     *
     * @return array
     */
    protected static function getCommonFieldConfig(): array
    {
        return [
            'wrapper' => [
                'class' => 'product-info-field ' . static::getProductType() . '-field',
            ],
            'conditional_logic' => create_acf_conditional_logic([static::getProductType()]),
        ];
    }

    /**
     * Create field with common configuration applied
     *
     * @param array $field Field configuration
     * @return array
     */
    protected static function createField(array $field): array
    {
        $commonConfig = self::getCommonFieldConfig();

        // Merge configurations, with field-specific config taking precedence
        return array_merge($commonConfig, $field);
    }
}
