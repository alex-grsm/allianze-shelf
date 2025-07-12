<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

abstract class BaseTab
{
    /**
     * Получить поля ACF для этого таба
     */
    abstract public static function getFields(): array;

    /**
     * Получить данные для продукта (для основного массива productAcfFields)
     */
    abstract public static function getDataForProduct(WC_Product $product): ?array;

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return null;
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return null;
    }

    /**
     * Зарегистрировать хуки (если нужны)
     */
    public static function registerHooks(): void
    {
        // По умолчанию ничего не делаем
    }

    /**
     * Проверить, включен ли таб для данного типа продукта
     */
    protected static function isEnabledForProductType(WC_Product $product, array $allowedTypes): bool
    {
        $productType = get_product_type($product);
        return in_array($productType, $allowedTypes);
    }

    /**
     * Получить conditional logic для типа продукта
     * @deprecated Используйте create_acf_conditional_logic() из helpers.php
     */
    protected static function getConditionalLogicForProductType(string $productType): array
    {
        return create_acf_conditional_logic([$productType]);
    }

    /**
     * Получить conditional logic для типа продукта + дополнительное условие
     * @deprecated Используйте create_acf_conditional_logic_for_types() из helpers.php
     */
    protected static function getConditionalLogicForProductTypeAndField(string $productType, string $fieldKey, string $value): array
    {
        return create_acf_conditional_logic_for_types(
            [$productType],
            ['field' => $fieldKey, 'value' => $value]
        );
    }

    /**
     * Получить conditional logic для нескольких типов продуктов
     * @deprecated Используйте create_acf_conditional_logic() из helpers.php
     */
    protected static function getConditionalLogicForProductTypes(array $productTypes): array
    {
        return create_acf_conditional_logic($productTypes);
    }

    /**
     * Получить conditional logic для нескольких типов продуктов + дополнительное условие
     * @deprecated Используйте create_acf_conditional_logic_for_types() из helpers.php
     */
    protected static function getConditionalLogicForProductTypesAndField(array $productTypes, string $fieldKey, string $value): array
    {
        return create_acf_conditional_logic_for_types(
            $productTypes,
            ['field' => $fieldKey, 'value' => $value]
        );
    }
}
