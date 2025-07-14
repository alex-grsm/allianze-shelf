<?php

namespace App\Admin;

/**
 * Enhanced Admin UI + Fixed Update Button + ACF Styles
 *
 * @package Sage
 * @version 1.0.0
 */
class AdminEnhancements
{
    /**
     * Регистрация всех админских улучшений
     */
    public static function register(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminAssets']);
    }

    /**
     * Подключение админских стилей и скриптов
     */
    public static function enqueueAdminAssets($hook): void
    {
        // Only on post edit pages
        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        // Only for posts, pages, and products
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->post_type, ['post', 'page', 'product'])) {
            return;
        }

        self::addUpdateButtonStyles();
        self::addUpdateButtonScript();

        // Enhanced ACF Styles for Products
        if (get_post_type() === 'product') {
            self::addProductAcfStyles();
            self::addProductAcfScripts();
        }
    }

    /**
     * Добавление стилей для кнопки обновления
     */
    private static function addUpdateButtonStyles(): void
    {
        wp_add_inline_style('admin-bar', '
            #fixed-update-btn {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 99999;
                background: #ffe512;
                color: black;
                border: none;
                border-radius: 8px;
                padding: 12px 20px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0,0,0,0.7);
                transition: all 0.3s ease;
            }

            #fixed-update-btn:hover {
                background: #135e96;
                color: #fff;
                transform: translateY(-2px);
            }

            #fixed-update-btn:active {
                transform: translateY(0);
            }

            @media (max-width: 782px) {
                #fixed-update-btn {
                    bottom: 20px;
                    right: 20px;
                    padding: 10px 16px;
                    font-size: 13px;
                }
            }

            @media print {
                #fixed-update-btn {
                    display: none !important;
                }
            }
        ');
    }

    /**
     * Добавление скрипта для кнопки обновления
     */
    private static function addUpdateButtonScript(): void
    {
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Create button
                $("body").append("<button type=\"button\" id=\"fixed-update-btn\">Update</button>");

                // Button click
                $("#fixed-update-btn").on("click", function() {
                    var mainButton = $("#publish, #save-post").first();
                    if (mainButton.length) {
                        mainButton.click();
                    }
                });
            });
        ');
    }

    /**
     * Добавление стилей для ACF полей продуктов
     */
    private static function addProductAcfStyles(): void
    {
        wp_add_inline_style('admin-bar', self::getProductAcfStyles());
    }

    /**
     * Добавление скриптов для ACF полей продуктов
     */
    private static function addProductAcfScripts(): void
    {
        wp_add_inline_script('jquery', self::getProductAcfScripts());
    }

    /**
     * Получение CSS стилей для ACF полей продуктов
     */
    private static function getProductAcfStyles(): string
    {
        return '
            /* ===== GENERAL POSTBOX STYLES ===== */
            .postbox {
                border-radius: 8px;
                overflow: hidden;
                background: #eaefff;
            }

            .acf-postbox.postbox.closed .hndle{
                background: #ffe512 !important;
                color: black !important;
            }

            .postbox .hndle,
            .woocommerce-product-description .postbox-header{
                background: #3498db !important;
                color: white !important;
                font-weight: 600 !important;
                font-size: 16px !important;
                padding: 15px 20px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__clear {
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                background: red;
                color: #fff;
                border-radius: 100%;
                margin-right: 10px;
                margin-top: 4px;
                line-height: 1;
            }

            /* ===== ENHANCED ACF TAB STYLES ===== */
            .acf-fields > .acf-tab-wrap {
                background: #f8f9fa;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin: 15px 0;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group {
                margin-bottom: 0 !important;
                padding: 0 !important;
                border: none !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                flex-wrap: wrap;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li {
                margin: 0 !important;
                border: none !important;
                flex: 1;
                min-width: 80px;
                position: relative;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li a {
                background: transparent !important;
                border: none !important;
                border-radius: 0 !important;
                color: rgba(255,255,255,0.8) !important;
                font-size: 11px !important;
                padding: 8px 6px !important;
                position: relative;
                transition: all 0.3s ease;
                text-align: center;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                display: block;
                border-right: 1px solid rgba(255,255,255,0.1);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li:last-child a {
                border-right: none;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li a:hover {
                color: white !important;
                background: rgba(255,255,255,0.1) !important;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li.active a {
                background: rgba(255,255,255,0.2) !important;
                color: white !important;
                font-weight: 600 !important;
                font-size: 11px !important;
            }

            .acf-fields > .acf-tab-wrap .acf-tab-group li.active a::after {
                content: "";
                position: absolute;
                left: 0;
                bottom: 0;
                height: 3px;
                width: 100%;
                background: #ffe512;
                box-shadow: 0 2px 4px rgba(255,229,18,0.4);
            }

            /* Tab Content Area */
            .acf-fields > .acf-tab-wrap .acf-tab-content {
                background: white;
                padding: 20px;
                border-radius: 0 0 8px 8px;
            }

            /* Responsive Tabs */
            @media (max-width: 1400px) {
                .acf-fields > .acf-tab-wrap .acf-tab-group li a {
                    font-size: 10px !important;
                    padding: 8px 4px !important;
                    letter-spacing: 0.2px;
                }
            }

            @media (max-width: 1200px) {
                .acf-fields > .acf-tab-wrap .acf-tab-group {
                    flex-wrap: wrap;
                }

                .acf-fields > .acf-tab-wrap .acf-tab-group li {
                    flex: 1 1 calc(25% - 1px);
                    min-width: 70px;
                }

                .acf-fields > .acf-tab-wrap .acf-tab-group li a {
                    padding: 8px 4px !important;
                    font-size: 10px !important;
                }
            }

            @media (max-width: 900px) {
                .acf-fields > .acf-tab-wrap .acf-tab-group li {
                    flex: 1 1 calc(33.333% - 1px);
                    min-width: 60px;
                }

                .acf-fields > .acf-tab-wrap .acf-tab-group li a {
                    padding: 6px 3px !important;
                    font-size: 9px !important;
                    letter-spacing: 0.1px;
                }
            }

            @media (max-width: 782px) {
                .acf-fields > .acf-tab-wrap .acf-tab-group li {
                    flex: 1 1 calc(50% - 1px);
                    min-width: 50px;
                }

                .acf-fields > .acf-tab-wrap .acf-tab-group li a {
                    padding: 6px 2px !important;
                    font-size: 8px !important;
                    letter-spacing: 0px;
                }
            }

            /* ===== ENHANCED PRODUCT TYPE FIELD STYLES ===== */
            .product-type-field .acf-button-group {
                display: flex;
                gap: 15px;
                margin-top: 10px;
            }

            .product-type-field .acf-button-group label {
                flex: 1;
                margin: 0 !important;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 8px !important;
                border: 2px solid #e5e7eb !important;
                background: #ffffff !important;
                padding: 12px 15px !important;
                text-align: center;
                position: relative;
                overflow: hidden;
                font-weight: 600;
                font-size: 14px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                min-height: 60px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .product-type-field .acf-button-group label:hover {
                border-color: #3b82f6 !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(59,130,246,0.15);
            }

            .product-type-field .acf-button-group label.selected {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
                color: white !important;
                border-color: #1d4ed8 !important;
                box-shadow: 0 4px 12px rgba(59,130,246,0.3);
                transform: translateY(-1px);
            }

            .product-type-field .acf-button-group label.selected::before {
                content: "✓";
                position: absolute;
                top: 8px;
                right: 12px;
                font-size: 16px;
                font-weight: bold;
            }

            .product-type-field .acf-button-group input[type="radio"] {
                display: none !important;
            }

            /* Product Type Icons */
            .product-type-field .acf-button-group label[data-value="companies"]::after {
                content: "📢";
                display: block;
                font-size: 18px;
                margin-bottom: 4px;
                order: -1;
            }

            .product-type-field .acf-button-group label[data-value="social_media_assets"]::after {
                content: "📱";
                display: block;
                font-size: 18px;
                margin-bottom: 4px;
                order: -1;
            }

            .product-type-field .acf-button-group label[data-value="newsletter"]::after {
                content: "📧";
                display: block;
                font-size: 18px;
                margin-bottom: 4px;
                order: -1;
            }

            .product-type-field .acf-button-group label[data-value="landing_page"]::after {
               content: "🌐";
               display: block;
               font-size: 18px;
               margin-bottom: 4px;
               order: -1;
            }

            /* Animation */
            .product-type-field .acf-button-group label {
                animation: fadeInUp 0.3s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Product Type Field Label Enhancement */
            .product-type-field .acf-label {
                font-size: 16px !important;
                font-weight: 600 !important;
                color: #2c3e50 !important;
                margin-bottom: 10px !important;
            }

            .product-type-field .acf-input .description {
                background: #f8f9fa;
                border-left: 4px solid #3b82f6;
                padding: 10px 15px;
                margin-top: 10px;
                border-radius: 0 6px 6px 0;
                font-style: italic;
                color: #495057;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .product-type-field .acf-button-group {
                    flex-direction: column;
                    gap: 10px;
                }

                .product-type-field .acf-button-group label {
                    padding: 10px 12px !important;
                    min-height: 50px;
                }

                .product-type-field .acf-button-group label[data-value="companies"]::after,
                .product-type-field .acf-button-group label[data-value="social_media_assets"]::after,
                .product-type-field .acf-button-group label[data-value="newsletter"]::after {
                    font-size: 16px;
                    margin-bottom: 3px;
                }
            }

            /* Enhanced Tab Transitions */
            .acf-tab-wrap .acf-tab-group li a {
                border-radius: 8px 8px 0 0;
                transition: all 0.2s ease;
            }

            .acf-tab-wrap .acf-tab-group li.active a {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
            }
        ';
    }

    /**
     * Получение JavaScript кода для ACF полей продуктов
     */
    private static function getProductAcfScripts(): string
    {
        return '
            jQuery(document).ready(function($) {
                // Initialize Product Type Field Enhancements
                function initProductTypeField() {
                    // Add data-value attributes for styling
                    const productTypeLabels = $(".product-type-field .acf-button-group label");
                    productTypeLabels.each(function() {
                        const input = $(this).find("input[type=radio]");
                        if (input.length) {
                            $(this).attr("data-value", input.val());
                        }
                    });

                    // Add change handler for smooth transitions
                    const productTypeInputs = $(".product-type-field input[type=radio]");
                    productTypeInputs.on("change", function() {
                        // Add transition effect when switching
                        const fieldGroup = $(this).closest(".acf-fields");
                        if (fieldGroup.length) {
                            fieldGroup.css("opacity", "0.7");
                            setTimeout(function() {
                                fieldGroup.css("opacity", "1");
                            }, 150);
                        }
                    });
                }

                // Initialize Tab Enhancements
                function initTabEnhancements() {
                    // Add smooth transition for tab content
                    $(".acf-tab-group li a").on("click", function() {
                        setTimeout(function() {
                            $(".acf-tab-content").css({
                                "opacity": "0",
                                "transform": "translateY(10px)"
                            }).animate({
                                "opacity": "1"
                            }, 200).css("transform", "translateY(0)");
                        }, 50);
                    });
                }

                // Initialize on page load
                initProductTypeField();
                initTabEnhancements();

                // Re-initialize when ACF fields are dynamically loaded
                $(document).on("acf/setup_fields", function() {
                    initProductTypeField();
                    initTabEnhancements();
                });
            });
        ';
    }
}
