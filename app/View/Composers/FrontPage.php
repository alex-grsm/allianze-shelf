<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class FrontPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'front-page',
        'partials.hero-home',
    ];

    /**
     * Register ACF fields for front page
     */
    public static function register(): void
    {
        // Auto-registration handled by Sage service provider
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        add_action('acf/init', [self::class, 'registerAcfFields']);
    }

    /**
     * Register ACF fields
     */
    public static function registerAcfFields(): void
    {
            acf_add_local_field_group([
                'key' => 'group_front_page_hero',
                'title' => 'Front Page Hero Settings',
                'fields' => [
                    [
                        'key' => 'field_hero_enabled',
                        'label' => 'Enable Hero Section',
                        'name' => 'hero_enabled',
                        'type' => 'true_false',
                        'instructions' => 'Enable/disable the hero section on front page',
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                        'ui_on_text' => 'Enabled',
                        'ui_off_text' => 'Disabled',
                    ],
                    [
                        'key' => 'field_hero_slides',
                        'label' => 'Hero Slides',
                        'name' => 'hero_slides',
                        'type' => 'repeater',
                        'instructions' => 'Add slides for the hero section. Minimum 1 slide required.',
                        'required' => 1,
                        'min' => 1,
                        'max' => 5,
                        'layout' => 'block',
                        'button_label' => 'Add Slide',
                        'collapsed' => 'field_slide_title',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hero_enabled',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                        'sub_fields' => [
                            [
                                'key' => 'field_slide_enabled',
                                'label' => 'Slide Enabled',
                                'name' => 'slide_enabled',
                                'type' => 'true_false',
                                'instructions' => 'Enable this slide',
                                'default_value' => 1,
                                'ui' => 1,
                                'ui_on_text' => 'Enabled',
                                'ui_off_text' => 'Disabled',
                                'wrapper' => ['width' => '20'],
                            ],
                            [
                                'key' => 'field_slide_title',
                                'label' => 'Slide Title',
                                'name' => 'slide_title',
                                'type' => 'text',
                                'instructions' => 'Main heading for the slide',
                                'required' => 1,
                                'maxlength' => 100,
                                'placeholder' => 'e.g., Always drive well',
                                'wrapper' => ['width' => '80'],
                            ],
                            [
                                'key' => 'field_slide_badge',
                                'label' => 'Badge Text',
                                'name' => 'slide_badge',
                                'type' => 'text',
                                'instructions' => 'Small text in the badge above title',
                                'required' => 0,
                                'maxlength' => 50,
                                'placeholder' => 'e.g., Campaign',
                                'wrapper' => ['width' => '50'],
                            ],
                            [
                                'key' => 'field_slide_show_rating',
                                'label' => 'Show Rating Stars',
                                'name' => 'slide_show_rating',
                                'type' => 'true_false',
                                'instructions' => 'Show rating stars next to badge',
                                'default_value' => 1,
                                'ui' => 1,
                                'ui_on_text' => 'Show',
                                'ui_off_text' => 'Hide',
                                'wrapper' => ['width' => '50'],
                            ],
                            [
                                'key' => 'field_slide_description',
                                'label' => 'Description',
                                'name' => 'slide_description',
                                'type' => 'textarea',
                                'instructions' => 'Slide description text',
                                'required' => 1,
                                'rows' => 3,
                                'maxlength' => 300,
                                'placeholder' => 'Germany\'s "Immer gut fahren" campaign highlights safety and coverage for drivers.',
                            ],
                            [
                                'key' => 'field_slide_background_image',
                                'label' => 'Background Image',
                                'name' => 'slide_background_image',
                                'type' => 'image',
                                'instructions' => 'Upload background image for this slide (recommended: 1920x1080px)',
                                'required' => 1,
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                                'library' => 'all',
                            ],
                            [
                                'key' => 'field_slide_cta_text',
                                'label' => 'CTA Button Text',
                                'name' => 'slide_cta_text',
                                'type' => 'text',
                                'instructions' => 'Text for the call-to-action button',
                                'required' => 0,
                                'maxlength' => 50,
                                'placeholder' => 'See details →',
                                'default_value' => 'See details →',
                                'wrapper' => ['width' => '50'],
                            ],
                            [
                                'key' => 'field_slide_cta_url',
                                'label' => 'CTA Button URL',
                                'name' => 'slide_cta_url',
                                'type' => 'url',
                                'instructions' => 'URL for the call-to-action button',
                                'required' => 0,
                                'placeholder' => '#details',
                                'wrapper' => ['width' => '50'],
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'page_type',
                            'operator' => '==',
                            'value' => 'front_page',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'active' => true,
                'description' => 'Configure the hero section slides for the front page',
            ]);
    }

    /**
     * Data that should be available to the view.
     */
    public function with()
    {
        // Only for front page
        if (!is_front_page()) {
            return [];
        }

        return [
            'heroData' => $this->getHeroData(),
        ];
    }

    /**
     * Get hero section data
     */
    private function getHeroData(): array
    {
        $heroEnabled = get_field('hero_enabled', get_option('page_on_front'));

        if (!$heroEnabled) {
            return [
                'enabled' => false,
                'slides' => [],
            ];
        }

        $slides = get_field('hero_slides', get_option('page_on_front'));

        if (!$slides || !is_array($slides)) {
            // No ACF data - return disabled state
            return [
                'enabled' => false,
                'slides' => [],
            ];
        }

        $formattedSlides = [];

        foreach ($slides as $slide) {
            // Only include enabled slides
            if (empty($slide['slide_enabled'])) {
                continue;
            }

            $formattedSlides[] = [
                'title' => $slide['slide_title'] ?? '',
                'badge' => $slide['slide_badge'] ?? '',
                'show_rating' => $slide['slide_show_rating'] ?? true,
                'description' => $slide['slide_description'] ?? '',
                'background_image' => $this->formatImageData($slide['slide_background_image']),
                'cta_text' => $slide['slide_cta_text'] ?? 'See details →',
                'cta_url' => $slide['slide_cta_url'] ?? '#',
            ];
        }

        // Ensure we have at least one enabled slide
        if (empty($formattedSlides)) {
            return [
                'enabled' => false,
                'slides' => [],
            ];
        }

        return [
            'enabled' => true,
            'slides' => $formattedSlides,
            'total_slides' => count($formattedSlides),
            'has_multiple_slides' => count($formattedSlides) > 1,
        ];
    }

    /**
     * Format image data for consistent output
     */
    private function formatImageData($imageField): ?array
    {
        if (!is_array($imageField) || empty($imageField['url'])) {
            return null;
        }

        return [
            'id' => $imageField['id'] ?? 0,
            'url' => $imageField['url'],
            'alt' => $imageField['alt'] ?? '',
            'title' => $imageField['title'] ?? '',
            'sizes' => $imageField['sizes'] ?? [],
            'width' => $imageField['width'] ?? 0,
            'height' => $imageField['height'] ?? 0,
        ];
    }
}
