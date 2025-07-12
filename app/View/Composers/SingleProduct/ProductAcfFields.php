<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

// Импортируем табы
use App\View\Composers\SingleProduct\Tabs\CompaniesInfoTab;
use App\View\Composers\SingleProduct\Tabs\BuyoutDetailsTab;
use App\View\Composers\SingleProduct\Tabs\AssetOverviewTab;
use App\View\Composers\SingleProduct\Tabs\ProductChannelsTab;
use App\View\Composers\SingleProduct\Tabs\ProductLinksTab;
use App\View\Composers\SingleProduct\Tabs\AttachmentsTab;
use App\View\Composers\SingleProduct\Tabs\SocialMediaAssetsInfoTab;
use App\View\Composers\SingleProduct\Tabs\NewsletterInformationTab;
use App\View\Composers\SingleProduct\Tabs\LandingPageInformationTab;

class ProductAcfFields extends Composer
{
    protected static $views = [
        'partials.single-product.product-summary',
        'partials.product-card',
        'partials.single-product.buyout-details',
        'partials.single-product.asset-overview',
        'partials.single-product.product-channels',
        'partials.single-product.product-links',
        'partials.single-product.attachments',
    ];

    /**
     * Все доступные табы
     */
    private static $allTabs = [
        CompaniesInfoTab::class,
        SocialMediaAssetsInfoTab::class,
        NewsletterInformationTab::class,
        LandingPageInformationTab::class,
        BuyoutDetailsTab::class,
        AssetOverviewTab::class,
        ProductChannelsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class
    ];

    /**
     * Порядок табов для типа Companies
     */
    private static $companiesTabsOrder = [
        CompaniesInfoTab::class,
        BuyoutDetailsTab::class,
        AssetOverviewTab::class,
        ProductChannelsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class
    ];

    /**
     * Порядок табов для типа Social Media Assets
     */
    private static $socialMediaTabsOrder = [
        SocialMediaAssetsInfoTab::class,
        BuyoutDetailsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class,
    ];

    /**
     * Порядок табов для типа Newsletter
     */
    private static $newsletterTabsOrder = [
        NewsletterInformationTab::class,
        BuyoutDetailsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class,
    ];

    /**
     * Порядок табов для типа Landing Page
     */
    private static $landingPageTabsOrder = [
        LandingPageInformationTab::class,
        BuyoutDetailsTab::class,
        ProductLinksTab::class,
        AttachmentsTab::class,
    ];

    /**
     * Register composer and ACF fields
     */
    public static function register(): void
    {
        // Register ACF fields
        static::registerAcfFields();

        // Регистрируем хуки для всех табов
        static::registerTabHooks();

        // Composer will be registered automatically through Sage
    }

    public function with()
    {
        // Проверяем WooCommerce
        if (!function_exists('wc_get_product')) {
            return $this->getEmptyData();
        }

        global $product;

        // Если это не страница товара, пытаемся получить товар из текущего поста в цикле
        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        // Если всё ещё нет товара, возвращаем null
        if (!$product instanceof WC_Product) {
            return $this->getEmptyData();
        }

        // Получаем тип продукта
        $productType = $this->getProductType($product);

        // Определяем порядок табов в зависимости от типа продукта
        $tabsOrder = $this->getTabsOrder($productType);

        // Собираем данные от всех табов
        $data = [
            'productAcfFields' => [
                'product_type' => $productType,
                'tabs_order' => $this->getTabsInfo($tabsOrder, $product),
            ]
        ];

        // Получаем данные от каждого таба в правильном порядке
        foreach ($tabsOrder as $tabClass) {
            $tabData = $tabClass::getDataForProduct($product);
            if ($tabData) {
                $data['productAcfFields'] = array_merge($data['productAcfFields'], $tabData);
            }

            // Получаем данные для отдельных переменных (для совместимости)
            $templateData = $tabClass::getTemplateData($product);
            if ($templateData) {
                $data = array_merge($data, $templateData);
            }
        }

        return $data;
    }

    /**
     * Получить порядок табов в зависимости от типа продукта
     */
    private function getTabsOrder(string $productType): array
    {
        return match ($productType) {
            'social_media_assets' => self::$socialMediaTabsOrder,
            'newsletter' => self::$newsletterTabsOrder,
            'landing_page' => self::$landingPageTabsOrder,
            'companies' => self::$companiesTabsOrder,
            default => self::$companiesTabsOrder,
        };
    }

    /**
     * Получить информацию о табах для фронтенда
     */
    private function getTabsInfo(array $tabsOrder, WC_Product $product): array
    {
        $tabsInfo = [];
        $activeTabSet = false;

        foreach ($tabsOrder as $index => $tabClass) {
            // Определяем является ли таб видимым для текущего типа продукта
            $isVisible = $this->isTabVisible($tabClass, $product);

            $tabInfo = [
                'class' => $tabClass,
                'name' => $this->getTabName($tabClass),
                'is_visible' => $isVisible,
                'is_active' => false,
                'order' => $index,
            ];

            // Делаем первый видимый таб активным
            if ($isVisible && !$activeTabSet) {
                $tabInfo['is_active'] = true;
                $activeTabSet = true;
            }

            $tabsInfo[] = $tabInfo;
        }

        return $tabsInfo;
    }

    /**
     * Проверить, виден ли таб для данного типа продукта
     */
    private function isTabVisible(string $tabClass, WC_Product $product): bool
    {
        $productType = $this->getProductType($product);

        // Проверяем видимость табов в зависимости от типа продукта
        return match ($tabClass) {
            CompaniesInfoTab::class => $productType === 'companies',
            SocialMediaAssetsInfoTab::class => $productType === 'social_media_assets',
            NewsletterInformationTab::class => $productType === 'newsletter',
            LandingPageInformationTab::class => $productType === 'landing_page',
            default => true, // Остальные табы видны для всех типов
        };
    }

    /**
     * Получить название таба
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
            // Базовые поля
            $fields = [
                // Улучшенное поле выбора типа продукта
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

            // Добавляем поля от каждого таба в правильном порядке
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
     * Регистрируем хуки для всех табов
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
     * Get product type
     */
    private function getProductType(WC_Product $product): string
    {
        return get_field('product_type', $product->get_id()) ?: 'companies';
    }

    /**
     * Return empty data structure
     */
    private function getEmptyData(): array
    {
        $emptyData = ['productAcfFields' => null];

        // Добавляем пустые данные для каждого таба
        foreach (self::$allTabs as $tabClass) {
            $tabEmptyData = $tabClass::getEmptyTemplateData();
            if ($tabEmptyData) {
                $emptyData = array_merge($emptyData, $tabEmptyData);
            }
        }

        return $emptyData;
    }
}
