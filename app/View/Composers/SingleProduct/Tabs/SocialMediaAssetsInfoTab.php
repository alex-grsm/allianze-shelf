<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class SocialMediaAssetsInfoTab extends BaseInfoTab
{
    /**
     * Тип продукта, к которому относится вкладка.
     */
    protected static function getProductType(): string
    {
        return 'social_media_assets';
    }

    /**
     * Название вкладки для UI.
     */
    protected static function getTabName(): string
    {
        return 'Social Media Assets Information';
    }

    /**
     * Дополнительные поля ACF для социальных медиа-ресурсов.
     *
     * Стандартные поля из BaseInfoTab (префикс 'sma_'):
     * - Country of Origin (sma_product_country_code)
     * - Rights Valid Until (sma_rights_until_date)
     * - Target (sma_product_target)
     * - Year (sma_product_year)
     * - Buyout (sma_product_buyout)
     * - Product Label (sma_product_label)
     *
     * Дополнительно:
     * - Platform Name
     * - Post Format Description
     */
    protected static function getAdditionalFields(): array
    {
        // $conditionalLogic = create_acf_conditional_logic(['social_media_assets']);

        return [];
    }

    /**
     * Дополнительные данные для социальных медиа-ресурсов.
     *
     * Базовые поля (префикс 'sma_'):
     * - sma_country_code
     * - sma_country_flag_url
     * - sma_rights_until_date
     * - sma_rights_until_formatted
     * - sma_target
     * - sma_year
     * - sma_buyout
     * - sma_label
     */
}
