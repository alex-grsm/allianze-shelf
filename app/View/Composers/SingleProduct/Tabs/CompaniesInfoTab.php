<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class CompaniesInfoTab extends BaseInfoTab
{
    /**
     * Указывает тип продукта, к которому относится вкладка.
     */
    protected static function getProductType(): string
    {
        return 'companies';
    }

    /**
     * Название вкладки для отображения на UI.
     */
    protected static function getTabName(): string
    {
        return 'Companies Information';
    }

    /**
     * Дополнительные ACF-поля, специфичные для вкладки Companies.
     *
     * Стандартные поля обрабатываются в BaseInfoTab с префиксом 'companies_':
     * - Country of Origin (companies_product_country_code)
     * - Rights Valid Until (companies_rights_until_date)
     * - Target (companies_product_target)
     * - Year (companies_product_year)
     * - Buyout (companies_product_buyout)
     * - Product Label (companies_product_label)
     *
     * Дополнительно можно добавить:
     * - Company Website
     * - Company Description
     */
    protected static function getAdditionalFields(): array
    {
        // $conditionalLogic = create_acf_conditional_logic(['companies']);

        return [
            // [
            //     'key' => 'field_companies_website',
            //     'label' => 'Company Website',
            //     'name' => 'companies_website',
            //     'type' => 'url',
            //     'default_value' => '',
            //     'placeholder' => 'https://example.com',
            //     'instructions' => 'Enter the official website of the company',
            //     'conditional_logic' => $conditionalLogic,
            // ],
            // [
            //     'key' => 'field_companies_description',
            //     'label' => 'Company Description',
            //     'name' => 'companies_description',
            //     'type' => 'textarea',
            //     'default_value' => '',
            //     'rows' => 4,
            //     'placeholder' => 'Enter company description...',
            //     'instructions' => 'Brief overview of the company and its business',
            //     'maxlength' => 500,
            //     'conditional_logic' => $conditionalLogic,
            // ],
        ];
    }

    /**
     * Получение дополнительных данных по продукту (по ACF).
     *
     * Базовые поля обрабатываются в BaseInfoTab с префиксом 'companies_':
     * - companies_country_code
     * - companies_country_flag_url
     * - companies_rights_until_date
     * - companies_rights_until_formatted
     * - companies_target
     * - companies_year
     * - companies_buyout
     * - companies_label
     *
     * Плюс:
     * - companies_website
     * - companies_description
     */
    // protected static function getAdditionalData(WC_Product $product): array
    // {
    //     return [
    //         'companies_website' => get_field('companies_website', $product->get_id()) ?: '',
    //         'companies_description' => get_field('companies_description', $product->get_id()) ?: '',
    //     ];
    // }
}
