<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class LandingPageInformationTab extends BaseInfoTab
{
    /**
     * Get the product type this tab handles
     */
    protected static function getProductType(): string
    {
        return 'landing_page';
    }

    /**
     * Get the tab display name
     */
    protected static function getTabName(): string
    {
        return 'Landing Page Information';
    }

    /**
     * Additional fields specific to Landing Page
     * Standard fields are handled by BaseInfoTab with 'landing_page_' prefix:
     * - Country of Origin (landing_page_product_country_code)
     * - Rights Valid Until (landing_page_rights_until_date)
     * - Target (landing_page_product_target)
     * - Year (landing_page_product_year)
     * - Buyout (landing_page_product_buyout)
     * - Product Label (landing_page_product_label)
     *
     * Plus these additional fields:
     * - Landing Page URL
     * - Landing Page Description
     */
    protected static function getAdditionalFields(): array
    {
        // $conditionalLogic = create_acf_conditional_logic(['landing_page']);

        return [];
    }

    /**
     * Additional data specific to Landing Page
     * Standard data is handled by BaseInfoTab with 'landing_page_' prefix:
     * - landing_page_country_code
     * - landing_page_country_flag_url
     * - landing_page_rights_until_date
     * - landing_page_rights_until_formatted
     * - landing_page_target
     * - landing_page_year
     * - landing_page_buyout
     * - landing_page_label
     */
}
