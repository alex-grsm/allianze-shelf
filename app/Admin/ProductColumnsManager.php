<?php

namespace App\Admin;

class ProductColumnsManager
{
    /**
     * Колонки которые нужно убрать
     */
    private static array $columnsToRemove = [
        'sku',                      // SKU колонка
        'product_tag',              // Tags колонка (стандартные WooCommerce теги)
        'taxonomy-product_brand',   // Brands колонка
    ];

    /**
     * Регистрация хуков
     */
    public static function register(): void
    {
        add_filter('manage_edit-product_columns', [self::class, 'customizeColumns']);
        add_filter('manage_edit-product_sortable_columns', [self::class, 'makeSortable']);
        add_action('admin_head', [self::class, 'addColumnStyles']);
    }

    /**
     * Кастомизация колонок - убираем нежелательные
     */
    public static function customizeColumns($columns)
    {
        foreach (self::$columnsToRemove as $column) {
            if (isset($columns[$column])) {
                unset($columns[$column]);
            }
        }

        return $columns;
    }

    /**
     * Делаем колонки сортируемыми
     */
    public static function makeSortable($columns)
    {
        $columns['price'] = 'price';
        return $columns;
    }

    /**
     * Стили для колонок
     */
    public static function addColumnStyles()
    {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'product' && $screen->base === 'edit') {
            ?>
            <style>
            /* Настройка ширины колонок */
            .wp-list-table .column-thumb { width: 60px; }
            .wp-list-table .column-name { width: 25%; }
            .wp-list-table .column-product_type { width: 80px; }
            .wp-list-table .column-price { width: 100px !important; }
            .wp-list-table .column-product_cat { width: 150px; }
            .wp-list-table .column-stock { width: 80px; }
            .wp-list-table .column-date { width: 120px; }

            /* Улучшения отображения */
            .wp-list-table .column-thumb img {
                border-radius: 4px;
                max-width: 50px;
                height: auto;
            }

            .wp-list-table .column-price {
                text-align: center;
                font-weight: 600;
            }

            .wp-list-table .column-stock {
                text-align: center;
            }

            /* Статусы наличия */
            .stock.in-stock {
                color: #7ad03a;
                font-weight: 600;
            }

            .stock.out-of-stock {
                color: #a00;
                font-weight: 600;
            }

            /* Скрываем фильтры по брендам через CSS (дополнительная защита) */
            select[name="product_brand"] {
                display: none !important;
            }
            </style>
            <?php
        }
    }

    /**
     * Получить список всех доступных колонок (для отладки)
     */
    public static function getAvailableColumns(): array
    {
        $columns = apply_filters('manage_edit-product_columns', []);
        return array_keys($columns);
    }

    /**
     * Добавить колонку в список удаляемых
     */
    public static function addColumnToRemove(string $columnKey): void
    {
        if (!in_array($columnKey, self::$columnsToRemove)) {
            self::$columnsToRemove[] = $columnKey;
        }
    }

    /**
     * Убрать колонку из списка удаляемых
     */
    public static function removeColumnFromRemoval(string $columnKey): void
    {
        $key = array_search($columnKey, self::$columnsToRemove);
        if ($key !== false) {
            unset(self::$columnsToRemove[$key]);
            self::$columnsToRemove = array_values(self::$columnsToRemove); // Переиндексация
        }
    }
}
