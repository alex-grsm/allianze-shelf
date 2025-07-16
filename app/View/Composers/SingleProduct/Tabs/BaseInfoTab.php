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

        // Get standard info fields
        $fields = static::createProductInfoFields($productType, $tabName);

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
     * Create standard info fields for product type
     * MOVED FROM helpers.php
     */
    protected static function createProductInfoFields(string $productType, string $tabName): array
    {
        $prefix = get_product_field_prefix($productType);
        $conditionalLogic = create_acf_conditional_logic([$productType]);

        return [
            [
                'key' => "field_{$productType}_info_tab",
                'label' => $tabName,
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}product_country_codes",
                'label' => 'Countries of Origin',
                'name' => "{$prefix}product_country_codes",
                'type' => 'select',
                'choices' => get_country_choices(),
                'default_value' => [],
                'allow_null' => 1,
                'multiple' => 1, // МНОЖЕСТВЕННЫЙ ВЫБОР
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select countries...',
                'instructions' => 'Select the countries where the product is manufactured or distributed',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}content_type",
                'label' => 'Content Type',
                'name' => "{$prefix}content_type",
                'type' => 'select',
                'choices' => [
                    'video' => 'Video',
                    'audio' => 'Audio',
                    'text' => 'Text',
                ],
                'default_value' => 'video',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select content type...',
                'instructions' => 'Select the primary content type for this product',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}rights_until_date",
                'label' => 'Rights Valid Until',
                'name' => "{$prefix}rights_until_date",
                'type' => 'date_picker',
                'display_format' => 'm/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
                'instructions' => 'Select the expiration date for product rights',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}product_target",
                'label' => 'Target',
                'name' => "{$prefix}product_target",
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter target information...',
                'instructions' => 'Specify the target information for this product',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}product_year",
                'label' => 'Year',
                'name' => "{$prefix}product_year",
                'type' => 'text',
                'default_value' => '',
                'maxlength' => 4,
                'placeholder' => 'YYYY',
                'instructions' => 'Enter the year for this product',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}product_buyout",
                'label' => 'Buyout',
                'name' => "{$prefix}product_buyout",
                'type' => 'text',
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => 'Enter buyout information...',
                'instructions' => 'Specify the buyout information for this product',
                'conditional_logic' => $conditionalLogic,
            ],
            [
                'key' => "field_{$prefix}product_label",
                'label' => 'Product Label',
                'name' => "{$prefix}product_label",
                'type' => 'select',
                'choices' => [
                    'featured' => 'Featured',
                    'easy_adaptable' => 'Easy adaptable',
                ],
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => 'Select label...',
                'instructions' => 'Choose a label for this product',
                'conditional_logic' => $conditionalLogic,
            ],
        ];
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
            'country_codes',
            'country_flags_urls',
            'primary_country_code',
            'primary_country_flag_url',
            'countries_display',
            'content_type',
            'content_type_label',
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
