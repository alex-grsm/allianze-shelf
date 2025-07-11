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
        $productType = get_field('product_type', $product->get_id()) ?: 'companies';
        return in_array($productType, $allowedTypes);
    }

    /**
     * Получить conditional logic для типа продукта
     */
    protected static function getConditionalLogicForProductType(string $productType): array
    {
        return [
            [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $productType,
                ],
            ],
        ];
    }

    /**
     * Получить conditional logic для типа продукта + дополнительное условие
     */
    protected static function getConditionalLogicForProductTypeAndField(string $productType, string $fieldKey, string $value): array
    {
        return [
            [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $productType,
                ],
                [
                    'field' => $fieldKey,
                    'operator' => '==',
                    'value' => $value,
                ],
            ],
        ];
    }

    /**
     * Получить conditional logic для нескольких типов продуктов
     */
    protected static function getConditionalLogicForProductTypes(array $productTypes): array
    {
        $conditions = [];
        foreach ($productTypes as $productType) {
            $conditions[] = [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $productType,
                ],
            ];
        }
        return $conditions;
    }

    /**
     * Получить conditional logic для нескольких типов продуктов + дополнительное условие
     */
    protected static function getConditionalLogicForProductTypesAndField(array $productTypes, string $fieldKey, string $value): array
    {
        $conditions = [];
        foreach ($productTypes as $productType) {
            $conditions[] = [
                [
                    'field' => 'field_product_type',
                    'operator' => '==',
                    'value' => $productType,
                ],
                [
                    'field' => $fieldKey,
                    'operator' => '==',
                    'value' => $value,
                ],
            ];
        }
        return $conditions;
    }
}
