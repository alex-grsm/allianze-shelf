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

        // Get unified meta data
        $metaData = get_product_meta_data($product);
        $prefix = get_product_field_prefix($productType);

        // Create data array with prefixed keys for backward compatibility
        $data = [];
        foreach ($metaData as $key => $value) {
            if (in_array($key, ['country_code', 'country_flag_url', 'rights_until_date', 'rights_until_formatted', 'target', 'year', 'buyout', 'label'])) {
                $prefixedKey = $prefix ? $prefix . $key : $key;
                $data[$prefixedKey] = $value;
            }
        }

        // Add any additional data specific to this tab
        $additionalData = static::getAdditionalData($product);
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        return $data;
    }

    /**
     * Get additional data specific to this tab (optional override)
     */
    protected static function getAdditionalData(WC_Product $product): array
    {
        return [];
    }

    /**
     * Get template data (for backward compatibility)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        // Most info tabs don't need separate template data
        return null;
    }

    /**
     * Get empty template data
     */
    public static function getEmptyTemplateData(): ?array
    {
        return null;
    }
}
