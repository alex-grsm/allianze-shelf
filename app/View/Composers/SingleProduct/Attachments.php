<?php

namespace App\View\Composers\SingleProduct;

use Roots\Acorn\View\Composer;
use WC_Product;

class Attachments extends Composer
{
    protected static $views = [
        'partials.single-product.attachments',
    ];

    /**
     * Register ACF fields and hooks
     */
    public static function register()
    {
        add_action('acf/init', [self::class, 'registerFields']);
        add_action('wp_insert_post', [self::class, 'setDefaultAttachments']);
        add_action('save_post_product', [self::class, 'ensureAttachmentsOnPublish']);
    }

    /**
     * Data for view
     */
    public function with()
    {
        if (!function_exists('is_product') || !is_product()) {
            return ['attachments' => null];
        }

        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product instanceof WC_Product) {
            return ['attachments' => null];
        }

        return [
            'attachments' => $this->getAttachmentData($product)
        ];
    }

    /**
     * Register ACF fields for attachments
     */
    public static function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_attachments',
            'title' => 'Attachments',
            'fields' => [
                [
                    'key' => 'field_attachments_description',
                    'label' => 'Attachments Description',
                    'name' => 'attachments_description',
                    'type' => 'textarea',
                    'instructions' => 'Enter description for attachments section.',
                    'required' => 0,
                    'rows' => 3,
                    'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                    'maxlength' => 400,
                ],
                [
                    'key' => 'field_product_attachments',
                    'label' => 'Product Attachments',
                    'name' => 'product_attachments',
                    'type' => 'repeater',
                    'instructions' => 'Add downloadable attachments with files and labels.',
                    'required' => 0,
                    'collapsed' => 'field_attachment_label',
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add Attachment',
                    'sub_fields' => [
                        [
                            'key' => 'field_attachment_label',
                            'label' => 'Attachment Label',
                            'name' => 'attachment_label',
                            'type' => 'text',
                            'instructions' => 'Enter label for this attachment (e.g.: Briefing Template, Reporting)',
                            'required' => 1,
                            'wrapper' => ['width' => '50'],
                            'placeholder' => 'e.g.: Briefing Template',
                            'maxlength' => 50,
                        ],
                        [
                            'key' => 'field_attachment_enabled',
                            'label' => 'Attachment Enabled',
                            'name' => 'attachment_enabled',
                            'type' => 'true_false',
                            'instructions' => 'Enable this attachment for download',
                            'wrapper' => ['width' => '50'],
                            'message' => 'Show attachment',
                            'default_value' => 1,
                            'ui' => 1,
                            'ui_on_text' => 'Yes',
                            'ui_off_text' => 'No',
                        ],
                        [
                            'key' => 'field_attachment_file',
                            'label' => 'Attachment File',
                            'name' => 'attachment_file',
                            'type' => 'file',
                            'instructions' => 'Upload file for this attachment (PDF, DOC, XLS, etc.).',
                            'required' => 1,
                            'return_format' => 'array',
                            'library' => 'all',
                            'mime_types' => 'pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt',
                        ],
                    ],
                ],
                [
                    'key' => 'field_attachments_enabled',
                    'label' => 'Enable Attachments Section',
                    'name' => 'attachments_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Enable to show attachments section on product page.',
                    'required' => 0,
                    'message' => 'Show attachments section on product page',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Yes',
                    'ui_off_text' => 'No',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ],
                ],
            ],
            'menu_order' => 1,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'description' => 'Configure downloadable attachments for product pages',
        ]);
    }

    /**
     * Set default attachments when creating product
     */
    public static function setDefaultAttachments($post_id)
    {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        // Only for new products
        if (get_post_status($post_id) === 'auto-draft' && !get_field('product_attachments', $post_id)) {
            $default_attachments = [
                [
                    'attachment_label' => 'Briefing Template',
                    'attachment_enabled' => true,
                ],
                [
                    'attachment_label' => 'Reporting',
                    'attachment_enabled' => true,
                ],
            ];

            update_field('product_attachments', $default_attachments, $post_id);
            update_field('attachments_description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', $post_id);
            update_field('attachments_enabled', true, $post_id);
        }
    }

    /**
     * Ensure published product has attachments
     */
    public static function ensureAttachmentsOnPublish($post_id)
    {
        if (get_post_status($post_id) !== 'publish') {
            return;
        }

        $attachments = get_field('product_attachments', $post_id);

        // If no attachments, add basic ones
        if (empty($attachments)) {
            $basic_attachments = [
                [
                    'attachment_label' => 'Briefing Template',
                    'attachment_enabled' => true,
                ],
            ];

            update_field('product_attachments', $basic_attachments, $post_id);
        }
    }

    /**
     * Get attachment data for product (for view)
     */
    private function getAttachmentData(WC_Product $product): ?array
    {
        $product_id = $product->get_id();

        // Check if attachments are enabled
        if (!self::isAttachmentsEnabled($product_id)) {
            return null;
        }

        $attachments = self::getAttachments($product_id);
        $description = get_field('attachments_description', $product_id);

        if (empty($attachments)) {
            return null;
        }

        // Format data for view - only enabled attachments with files
        $formatted_attachments = [];
        foreach ($attachments as $attachment) {
            if (!empty($attachment['attachment_enabled']) && !empty($attachment['attachment_file']) && !empty($attachment['attachment_label'])) {
                $formatted_attachments[] = [
                    'label' => $attachment['attachment_label'],
                    'file' => $attachment['attachment_file'],
                    'slug' => sanitize_title($attachment['attachment_label']),
                    'file_size' => self::formatFileSize($attachment['attachment_file']['filesize'] ?? 0),
                    'file_extension' => strtoupper(pathinfo($attachment['attachment_file']['filename'] ?? '', PATHINFO_EXTENSION)),
                ];
            }
        }

        if (empty($formatted_attachments)) {
            return null;
        }

        return [
            'description' => $description ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            'attachments' => $formatted_attachments,
            'total_count' => count($formatted_attachments),
            'has_attachments' => !empty($formatted_attachments),
        ];
    }

    /**
     * Format file size in human readable format
     */
    private static function formatFileSize($bytes): string
    {
        if ($bytes == 0) return '0 B';

        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 1) . ' ' . $sizes[$i];
    }

    /**
     * STATIC HELPERS FOR USE IN TEMPLATES
     */

    /**
     * Check if attachments are enabled for product
     */
    public static function isAttachmentsEnabled($product_id): bool
    {
        return (bool) get_field('attachments_enabled', $product_id);
    }

    /**
     * Get product attachments
     */
    public static function getAttachments($product_id): array
    {
        return get_field('product_attachments', $product_id) ?: [];
    }

    /**
     * Get only active attachments with files
     */
    public static function getActiveAttachments($product_id): array
    {
        $attachments = self::getAttachments($product_id);

        return array_filter($attachments, function($attachment) {
            return !empty($attachment['attachment_enabled']) && !empty($attachment['attachment_file']);
        });
    }

    /**
     * Get attachments statistics
     */
    public static function getStats($product_id): array
    {
        if (!self::isAttachmentsEnabled($product_id)) {
            return [
                'total' => 0,
                'active' => 0,
                'has_attachments' => false,
            ];
        }

        $attachments = self::getAttachments($product_id);
        $active_attachments = self::getActiveAttachments($product_id);

        return [
            'total' => count($attachments),
            'active' => count($active_attachments),
            'has_attachments' => count($active_attachments) > 0,
        ];
    }

    /**
     * Check if product has attachments with files
     */
    public static function hasAttachmentContent($product_id): bool
    {
        if (!self::isAttachmentsEnabled($product_id)) {
            return false;
        }

        $active_attachments = self::getActiveAttachments($product_id);
        return count($active_attachments) > 0;
    }
}
