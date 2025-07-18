<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use App\Taxonomies\ProductTagsHierarchy;
use App\WooCommerce\CartAjax;
use App\View\Composers\SingleProduct\ProductAcfFields;
use App\View\Composers\FrontPage;
use App\Admin\ProductColumnsManager;
use App\Admin\AdminEnhancements;
use App\Search\SearchService;
use App\Helpers\MenuHelper;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Регистрируем сервис поиска
        $this->registerSearchService();

        // Регистрируем сервис меню
        $this->registerMenuService();

        // Регистрируем сервис главной страницы
        $this->registerFrontPageService();

        // Регистрируем админские сервисы
        $this->registerAdminServices();

        // Регистрируем WooCommerce сервисы только если WooCommerce активен
        if ($this->isWooCommerceActive()) {
            $this->registerWooCommerceServices();
        }

        // Регистрируем остальные сервисы темы
        $this->registerThemeServices();
    }

    /**
     * Регистрация сервиса поиска
     */
    private function registerSearchService(): void
    {
        SearchService::register();
    }

    /**
     * Регистрация сервиса меню
     */
    private function registerMenuService(): void
    {
        MenuHelper::register();
    }

    /**
     * Регистрация сервиса главной страницы
     */
    private function registerFrontPageService(): void
    {
        FrontPage::register();
    }

    /**
     * Регистрация админских сервисов
     */
    private function registerAdminServices(): void
    {
        // Регистрируем админские улучшения только в админке
        if (is_admin()) {
            AdminEnhancements::register();
        }
    }

    /**
     * Регистрация WooCommerce сервисов
     */
    private function registerWooCommerceServices(): void
    {
        // Регистрируем кастомные таксономии
        ProductTagsHierarchy::register();

        // Регистрируем AJAX обработчики корзины
        CartAjax::register();

        // Регистрируем ACF поля продуктов
        ProductAcfFields::register();

        // Настраиваем админские колонки продуктов
        ProductColumnsManager::register();

        // Добавляем поддержку кастомных тегов в REST API
        add_action('rest_api_init', [$this, 'registerCustomRestFields']);
    }

    /**
     * Регистрация общих сервисов темы
     */
    private function registerThemeServices(): void
    {
        // Здесь можно добавить другие сервисы темы
        // Например: Performance, Security и т.д.

        // Пример:
        // $this->registerSeoServices();
        // $this->registerPerformanceOptimizations();
    }

    /**
     * Проверка активности WooCommerce
     */
    private function isWooCommerceActive(): bool
    {
        return class_exists('WooCommerce');
    }

    /**
     * Регистрация кастомных полей в REST API
     */
    public function registerCustomRestFields()
    {
        register_rest_field('product', 'product_tags_hierarchy', [
            'get_callback' => function ($object) {
                return ProductTagsHierarchy::getProductTags($object['id']);
            },
            'schema' => [
                'description' => __('Product hierarchy tags', 'sage'),
                'type'        => 'array',
                'context'     => ['view', 'edit'],
            ],
        ]);
    }

    /**
     * Пример дополнительных сервисов (можно раскомментировать при необходимости)
     */

    // /**
    //  * Регистрация оптимизаций производительности
    //  */
    // private function registerPerformanceOptimizations(): void
    // {
    //     // Оптимизации загрузки
    //     add_action('wp_enqueue_scripts', [$this, 'optimizeScriptLoading']);
    // }

    // /**
    //  * Добавление кастомных мета-тегов
    //  */
    // public function addCustomMetaTags()
    // {
    //     if (is_product()) {
    //         // Добавляем специальные мета-теги для продуктов
    //     }
    // }

    // /**
    //  * Оптимизация загрузки скриптов
    //  */
    // public function optimizeScriptLoading()
    // {
    //     // Отложенная загрузка скриптов
    //     if (!is_admin()) {
    //         // Логика оптимизации
    //     }
    // }
}
