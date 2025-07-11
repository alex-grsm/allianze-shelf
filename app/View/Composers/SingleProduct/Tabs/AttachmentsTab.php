<?php

namespace App\View\Composers\SingleProduct\Tabs;

use WC_Product;

class AttachmentsTab extends BaseTab
{
    /**
     * Получить поля ACF для таба Attachments
     */
    public static function getFields(): array
    {
        return [
            // ===== TAB: ATTACHMENTS =====
            [
                'key' => 'field_attachments_tab',
                'label' => 'Attachments',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets']),
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
                'conditional_logic' => self::getConditionalLogicForProductTypes(['companies', 'social_media_assets']),
            ],
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
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets'], 'field_attachments_enabled', '1'),
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
                'conditional_logic' => self::getConditionalLogicForProductTypesAndField(['companies', 'social_media_assets'], 'field_attachments_enabled', '1'),
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
        ];
    }

    /**
     * Получить данные для продукта
     */
    public static function getDataForProduct(WC_Product $product): ?array
    {
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets'])) {
            return null;
        }

        return [
            // Attachments
            'attachments_enabled' => self::isAttachmentsEnabled($product),
            'attachments_description' => self::getAttachmentsDescription($product),
            'attachments' => self::getFormattedAttachments($product),
            'attachments_stats' => self::getAttachmentsStats($product),
            'has_attachments_content' => self::hasAttachmentsContent($product),
        ];
    }

    /**
     * Получить данные для отдельных переменных шаблона (для совместимости)
     */
    public static function getTemplateData(WC_Product $product): ?array
    {
        return [
            'attachments' => self::getAttachmentsForTemplate($product),
        ];
    }

    /**
     * Получить пустые данные для шаблона
     */
    public static function getEmptyTemplateData(): ?array
    {
        return [
            'attachments' => null,
        ];
    }

    /**
     * Зарегистрировать хуки
     */
    public static function registerHooks(): void
    {
        add_action('wp_insert_post', [self::class, 'setDefaultAttachments']);
        add_action('save_post_product', [self::class, 'ensureAttachmentsOnPublish']);
    }

    // ===== PRIVATE METHODS =====

    /**
     * Check if attachments are enabled for product
     */
    private static function isAttachmentsEnabled(WC_Product $product): bool
    {
        return (bool) get_field('attachments_enabled', $product->get_id());
    }

    /**
     * Get attachments description
     */
    private static function getAttachmentsDescription(WC_Product $product): string
    {
        return get_field('attachments_description', $product->get_id()) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';
    }

    /**
     * Get product attachments
     */
    private static function getAttachments(WC_Product $product): array
    {
        return get_field('product_attachments', $product->get_id()) ?: [];
    }

    /**
     * Get formatted attachments for view
     */
    private static function getFormattedAttachments(WC_Product $product): array
    {
        $attachments = self::getAttachments($product);
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

        return $formatted_attachments;
    }

    /**
     * Get attachments statistics
     */
    private static function getAttachmentsStats(WC_Product $product): array
    {
        $attachments = self::getAttachments($product);
        $active_attachments = self::getActiveAttachments($product);

        return [
            'total' => count($attachments),
            'active' => count($active_attachments),
            'has_attachments' => count($active_attachments) > 0,
        ];
    }

    /**
     * Get only active attachments with files
     */
    private static function getActiveAttachments(WC_Product $product): array
    {
        $attachments = self::getAttachments($product);

        return array_filter($attachments, function($attachment) {
            return !empty($attachment['attachment_enabled']) && !empty($attachment['attachment_file']);
        });
    }

    /**
     * Check if product has attachments content
     */
    private static function hasAttachmentsContent(WC_Product $product): bool
    {
        if (!self::isAttachmentsEnabled($product)) {
            return false;
        }

        $active_attachments = self::getActiveAttachments($product);
        return count($active_attachments) > 0;
    }

    /**
     * Get attachments data formatted for template (compatibility with existing attachments.blade.php)
     */
    private static function getAttachmentsForTemplate(WC_Product $product): ?array
    {
        // Проверяем, включены ли вложения и тип продукта
        if (!self::isEnabledForProductType($product, ['companies', 'social_media_assets']) || !self::isAttachmentsEnabled($product)) {
            return null;
        }

        $formatted_attachments = self::getFormattedAttachments($product);

        if (empty($formatted_attachments)) {
            return null;
        }

        return [
            'description' => self::getAttachmentsDescription($product),
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

    // ===== STATIC METHODS FOR HOOKS =====

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

    // ===== STATIC HELPER METHODS FOR TEMPLATES =====

    /**
     * Get attachments for product (static method for template use)
     */
    public static function getAttachmentsForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        return self::getAttachments($product);
    }

    /**
     * Get active attachments for product (static method for template use)
     */
    public static function getActiveAttachmentsForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        return self::getActiveAttachments($product);
    }

    /**
     * Get stats for product (static method for template use)
     */
    public static function getStatsForProduct($product_id): array
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return [
                'total' => 0,
                'active' => 0,
                'has_attachments' => false,
            ];
        }

        return self::getAttachmentsStats($product);
    }

    /**
     * Check if attachments are enabled for product (static method for template use)
     */
    public static function isAttachmentsEnabledForProduct($product_id): bool
    {
        return (bool) get_field('attachments_enabled', $product_id);
    }

    /**
     * Check if product has attachment content (static method for template use)
     */
    public static function hasAttachmentContentForProduct($product_id): bool
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }

        return self::hasAttachmentsContent($product);
    }
}
