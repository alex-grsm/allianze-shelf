<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

// Импортируем табы
use App\View\Composers\SingleProduct\Tabs\CompaniesInfoTab;
use App\View\Composers\SingleProduct\Tabs\BuyoutDetailsTab;
use App\View\Composers\SingleProduct\Tabs\AssetOverviewTab;
use App\View\Composers\SingleProduct\Tabs\AssetOverviewListTab;
use App\View\Composers\SingleProduct\Tabs\ProductChannelsTab;
use App\View\Composers\SingleProduct\Tabs\ProductLinksTab;
use App\View\Composers\SingleProduct\Tabs\AttachmentsTab;
use App\View\Composers\SingleProduct\Tabs\SocialMediaAssetsInfoTab;
use App\View\Composers\SingleProduct\Tabs\NewsletterInformationTab;
use App\View\Composers\SingleProduct\Tabs\LandingPageInformationTab;

// Импортируем глобальные классы
use ProductTypes;
use ProductFieldPrefixes;

class ProductAcfFields extends Composer
{
    protected static $views = [
        'partials.single-product.product-summary',
        'partials.product-card',
        'partials.single-product.buyout-details',
        'partials.single-product.asset-overview',
        'partials.single-product.asset-overview-list',
        'partials.single-product.product-channels',
        'partials.single-product.product-links',
        'partials.single-product.attachments',
    ];

    /**
     * All available tabs
     */
    private static $allTabs = [
        CompaniesInfoTab::class,
        SocialMediaAssetsInfoTab::class,
        NewsletterInformationTab::class,
        LandingPageInformationTab::class,
        BuyoutDetailsTab::class,
        AssetOverviewTab::class,
        AssetOverviewListTab::class,
        ProductChannelsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class
    ];

    /**
     * Tab order configuration for each product type
     */
    private static $tabsOrderConfig = [
        'companies' => [
            CompaniesInfoTab::class,
            BuyoutDetailsTab::class,
            AssetOverviewTab::class,
            ProductChannelsTab::class,
            ProductLinksTab::class,
            AttachmentsTab::class
        ],
        'social_media_assets' => [
            SocialMediaAssetsInfoTab::class,
            BuyoutDetailsTab::class,
            AssetOverviewListTab::class,
            ProductLinksTab::class,
            AttachmentsTab::class,
        ],
        'newsletter' => [
            NewsletterInformationTab::class,
            BuyoutDetailsTab::class,
            AssetOverviewListTab::class,
            ProductLinksTab::class,
            AttachmentsTab::class,
        ],
        'landing_page' => [
            LandingPageInformationTab::class,
            BuyoutDetailsTab::class,
            AssetOverviewListTab::class,
            ProductLinksTab::class,
            AttachmentsTab::class,
        ],
    ];

    /**
     * Register composer and ACF fields
     */
    public static function register(): void
    {
        // Register ACF fields
        static::registerAcfFields();

        // Register tab hooks
        static::registerTabHooks();

        // Composer will be registered automatically through Sage
    }

    public function with()
    {
        // Check WooCommerce
        if (!function_exists('wc_get_product')) {
            return $this->getEmptyData();
        }

        global $product;

        // Get product from current post if not a product page
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        // If still no product, return empty data
        if (!$product instanceof WC_Product) {
            return $this->getEmptyData();
        }

        // Get product type using helper
        $productType = get_product_type($product);

        // Get tabs order for product type
        $tabsOrder = $this->getTabsOrder($productType);

        // Collect data from all tabs
        $data = [
            'productAcfFields' => [
                'product_type' => $productType,
                'tabs_order' => $this->getTabsInfo($tabsOrder, $product),
            ]
        ];

        // Add unified product meta data
        $productMeta = get_product_meta_data($product);
        $data['productAcfFields'] = array_merge($data['productAcfFields'], $productMeta);

        // Get data from each tab in correct order
        foreach ($tabsOrder as $tabClass) {
            $tabData = $tabClass::getDataForProduct($product);
            if ($tabData) {
                $data['productAcfFields'] = array_merge($data['productAcfFields'], $tabData);
            }

            // Get template data for separate variables (for compatibility)
            $templateData = $tabClass::getTemplateData($product);
            if ($templateData) {
                $data = array_merge($data, $templateData);
            }
        }

        return $data;
    }

    /**
     * Get tabs order based on product type
     */
    private function getTabsOrder(string $productType): array
    {
        return self::$tabsOrderConfig[$productType] ?? self::$tabsOrderConfig['companies'];
    }

    /**
     * Get tabs info for frontend
     */
    private function getTabsInfo(array $tabsOrder, WC_Product $product): array
    {
        $tabsInfo = [];
        $activeTabSet = false;

        foreach ($tabsOrder as $index => $tabClass) {
            // Check if tab is visible for current product type
            $isVisible = $this->isTabVisible($tabClass, $product);

            $tabInfo = [
                'class' => $tabClass,
                'name' => $this->getTabName($tabClass),
                'is_visible' => $isVisible,
                'is_active' => false,
                'order' => $index,
            ];

            // Make first visible tab active
            if ($isVisible && !$activeTabSet) {
                $tabInfo['is_active'] = true;
                $activeTabSet = true;
            }

            $tabsInfo[] = $tabInfo;
        }

        return $tabsInfo;
    }

    /**
     * Check if tab is visible for given product type
     */
    private function isTabVisible(string $tabClass, WC_Product $product): bool
    {
        $productType = get_product_type($product);

        return match ($tabClass) {
            CompaniesInfoTab::class => $productType === 'companies',
            SocialMediaAssetsInfoTab::class => $productType === 'social_media_assets',
            NewsletterInformationTab::class => $productType === 'newsletter',
            LandingPageInformationTab::class => $productType === 'landing_page',
            AssetOverviewTab::class => current_product_supports($product, 'asset_overview'),
            AssetOverviewListTab::class => in_array($productType, ['social_media_assets', 'newsletter', 'landing_page']), // Обновленное условие
            ProductChannelsTab::class => current_product_supports($product, 'channels'),
            default => true, // Other tabs are visible for all types
        };
    }

    /**
     * Get tab name
     */
    private function getTabName(string $tabClass): string
    {
        return match ($tabClass) {
            CompaniesInfoTab::class => 'companies_info',
            SocialMediaAssetsInfoTab::class => 'social_media_assets_info',
            NewsletterInformationTab::class => 'newsletter_info',
            LandingPageInformationTab::class => 'landing_page_info',
            BuyoutDetailsTab::class => 'buyout_details',
            AssetOverviewTab::class => 'asset_overview',
            AssetOverviewListTab::class => 'asset_overview_list', // Новое имя таба
            ProductChannelsTab::class => 'product_channels',
            ProductLinksTab::class => 'product_links',
            AttachmentsTab::class => 'attachments',
            default => 'unknown',
        };
    }

    /**
     * Register ACF fields programmatically
     */
    private static function registerAcfFields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        add_action('acf/init', function () {
            // Base fields
            $fields = [
                // Improved product type selection field
                [
                    'key' => 'field_product_type',
                    'label' => 'Product Type',
                    'name' => 'product_type',
                    'type' => 'button_group',
                    'choices' => [
                        'companies' => 'Companies',
                        'social_media_assets' => 'Social Media Assets',
                        'newsletter' => 'Newsletter',
                        'landing_page' => 'Landing Page',
                    ],
                    'default_value' => 'companies',
                    'allow_null' => 0,
                    'layout' => 'horizontal',
                    'return_format' => 'value',
                    'instructions' => 'Choose the type of product. This will determine which fields and sections are available for this product.',
                    'required' => 1,
                    'wrapper' => [
                        'width' => '',
                        'class' => 'product-type-field',
                        'id' => '',
                    ],
                ],
            ];

            // Add fields from each tab in correct order
            foreach (self::$allTabs as $tabClass) {
                $tabFields = $tabClass::getFields();
                if ($tabFields) {
                    $fields = array_merge($fields, $tabFields);
                }
            }

            acf_add_local_field_group([
                'key' => 'group_product_additional_info',
                'title' => 'Product Additional Information',
                'fields' => $fields,
                'location' => [
                    [
                        [
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'product',
                        ],
                    ],
                ],
                'menu_order' => 20,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'active' => true,
                'description' => 'Configure additional product information based on product type',
            ]);
        });
    }

    /**
     * Register hooks for all tabs
     */
    private static function registerTabHooks(): void
    {
        foreach (self::$allTabs as $tabClass) {
            if (method_exists($tabClass, 'registerHooks')) {
                $tabClass::registerHooks();
            }
        }
    }

    /**
     * Return empty data structure
     */
    private function getEmptyData(): array
    {
        $emptyData = ['productAcfFields' => null];

        // Add empty data for each tab
        foreach (self::$allTabs as $tabClass) {
            $tabEmptyData = $tabClass::getEmptyTemplateData();
            if ($tabEmptyData) {
                $emptyData = array_merge($emptyData, $tabEmptyData);
            }
        }

        return $emptyData;
    }
}
