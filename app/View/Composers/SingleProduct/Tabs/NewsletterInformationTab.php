<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class NewsletterInformationTab extends BaseInfoTab
{
    /**
     * Указывает тип продукта, к которому относится вкладка.
     */
    protected static function getProductType(): string
    {
        return 'newsletter'; // Или ProductTypes::NEWSLETTER, если используется enum
    }

    /**
     * Название вкладки для интерфейса.
     */
    protected static function getTabName(): string
    {
        return 'Newsletter Information';
    }

    /**
     * Дополнительные ACF-поля, специфичные для рассылок.
     *
     * Стандартные поля из BaseInfoTab (с префиксом 'newsletter_'):
     * - Country of Origin (newsletter_product_country_code)
     * - Rights Valid Until (newsletter_rights_until_date)
     * - Target (newsletter_product_target)
     * - Year (newsletter_product_year)
     * - Buyout (newsletter_product_buyout)
     * - Product Label (newsletter_product_label)
     */
    protected static function getAdditionalFields(): array
    {
        // $conditionalLogic = create_acf_conditional_logic(['newsletter']);

        return [];
    }

    /**
     * Дополнительные данные, специфичные для Newsletter.
     *
     * Стандартные поля из BaseInfoTab:
     * - newsletter_country_code
     * - newsletter_country_flag_url
     * - newsletter_rights_until_date
     * - newsletter_rights_until_formatted
     * - newsletter_target
     * - newsletter_year
     * - newsletter_buyout
     * - newsletter_label
     */
}
