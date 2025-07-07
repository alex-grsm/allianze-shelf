<?php

/**
 * Simple Fixed Update Button + ACF Styles
 *
 * @package Sage
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add fixed update button to admin
 */
add_action('admin_enqueue_scripts', function($hook) {
    // Only on post edit pages
    if (!in_array($hook, ['post.php', 'post-new.php'])) {
        return;
    }

    // Only for posts, pages, and products
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, ['post', 'page', 'product'])) {
        return;
    }

    // Add CSS for Update Button
    wp_add_inline_style('admin-bar', '
        #fixed-update-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99999;
            background: #2271b1;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        #fixed-update-btn:hover {
            background: #135e96;
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

    // Add JavaScript for Update Button
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

    // ACF Styles for Products
    if (get_post_type() === 'product') {
        wp_add_inline_style('admin-bar', '
            /* Asset Overview - Синий */
            .postbox {
                border-radius: 8px;
                overflow: hidden;
                background: #eaefff;
            }
            .postbox .hndle,
            .postbox .hndle,
            .woocommerce-product-description .postbox-header {
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
        ');
    }
});
