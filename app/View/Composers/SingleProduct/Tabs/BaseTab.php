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
     * По умолчанию возвращает null, переопределяется в дочерних классах при необходимости
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return null;
    }

    /**
     * Получить пустые данные для шаблона
     * По умолчанию возвращает null, переопределяется в дочерних классах при необходимости
     */
    public static function getEmptyTemplateData(): ?array
    {
        return null;
    }

    /**
     * Зарегистрировать хуки (если нужны)
     * По умолчанию ничего не делаем, переопределяется в дочерних классах при необходимости
     */
    public static function registerHooks(): void
    {
        // По умолчанию ничего не делаем
    }

    /**
     * Проверить, включен ли таб для данного типа продукта
     *
     * @param WC_Product $product
     * @param array $allowedTypes Массив разрешенных типов продуктов
     * @return bool
     */
    protected static function isEnabledForProductType(WC_Product $product, array $allowedTypes): bool
    {
        $productType = get_product_type($product);
        return in_array($productType, $allowedTypes);
    }

    /**
     * Проверить, поддерживает ли продукт конкретную функцию
     *
     * @param WC_Product $product
     * @param string $feature Название функции (например, 'asset_overview', 'channels')
     * @return bool
     */
    protected static function productSupportsFeature(WC_Product $product, string $feature): bool
    {
        return current_product_supports($product, $feature);
    }

    /**
     * Получить безопасное значение поля ACF
     *
     * @param string $fieldName Название поля
     * @param int $productId ID продукта
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    protected static function getFieldValue(string $fieldName, int $productId, $default = '')
    {
        $value = get_field($fieldName, $productId);
        return $value !== false ? $value : $default;
    }

    /**
     * Получить булево значение поля ACF
     *
     * @param string $fieldName Название поля
     * @param int $productId ID продукта
     * @param bool $default Значение по умолчанию
     * @return bool
     */
    protected static function getBooleanFieldValue(string $fieldName, int $productId, bool $default = false): bool
    {
        return (bool) self::getFieldValue($fieldName, $productId, $default);
    }

    /**
     * Получить массив из повторяющегося поля ACF
     *
     * @param string $fieldName Название поля
     * @param int $productId ID продукта
     * @return array
     */
    protected static function getRepeaterFieldValue(string $fieldName, int $productId): array
    {
        $value = get_field($fieldName, $productId);
        return is_array($value) ? $value : [];
    }

    /**
     * Проверить, имеет ли таб контент для отображения
     * Базовая реализация - проверяет, что данные не пустые
     *
     * @param WC_Product $product
     * @return bool
     */
    protected static function hasContent(WC_Product $product): bool
    {
        $data = static::getDataForProduct($product);
        return !empty($data);
    }

    /**
     * Форматировать файловые данные из ACF
     *
     * @param array|false $fileField Данные файла из ACF
     * @return array|null
     */
    protected static function formatFileData($fileField): ?array
    {
        if (!is_array($fileField) || empty($fileField['url'])) {
            return null;
        }

        return [
            'url' => $fileField['url'],
            'filename' => $fileField['filename'] ?? '',
            'filesize' => isset($fileField['filesize']) ? format_file_size($fileField['filesize']) : '',
            'mime_type' => $fileField['mime_type'] ?? '',
            'title' => $fileField['title'] ?? '',
            'alt' => $fileField['alt'] ?? '',
        ];
    }

    /**
     * Форматировать данные изображения из ACF
     *
     * @param array|false $imageField Данные изображения из ACF
     * @return array|null
     */
    protected static function formatImageData($imageField): ?array
    {
        if (!is_array($imageField) || empty($imageField['url'])) {
            return null;
        }

        return [
            'id' => $imageField['id'] ?? 0,
            'url' => $imageField['url'],
            'alt' => $imageField['alt'] ?? '',
            'caption' => $imageField['caption'] ?? '',
            'title' => $imageField['title'] ?? '',
            'sizes' => $imageField['sizes'] ?? [],
            'width' => $imageField['width'] ?? 0,
            'height' => $imageField['height'] ?? 0,
        ];
    }

    /**
     * Создать slug из строки
     *
     * @param string $string
     * @return string
     */
    protected static function createSlug(string $string): string
    {
        return sanitize_title($string);
    }

    /**
     * Получить конфигурацию по умолчанию для повторяющихся полей
     * Можно переопределить в дочерних классах для кастомизации
     *
     * @return array
     */
    protected static function getDefaultRepeaterConfig(): array
    {
        return [
            'min' => 0,
            'max' => 10,
            'layout' => 'block',
            'collapsed' => '',
        ];
    }

    /**
     * Логировать ошибку или предупреждение (для отладки)
     *
     * @param string $message
     * @param string $level 'error', 'warning', 'info'
     * @return void
     */
    protected static function log(string $message, string $level = 'info'): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[%s] %s: %s', strtoupper($level), static::class, $message));
        }
    }
}
