<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Request;

class Controller extends AbstractController
{
    /**
     * Fix Star Rating control when review form is used inside an Elementor Pro Popup.
     *
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterElementorPublicInlineScript(string $script): string
    {
        if (!defined('ELEMENTOR_VERSION')) {
            return $script;
        }
        $inlineScript = (string) file_get_contents(glsr()->path('assets/scripts/integrations/elementor-frontend.js'));
        return $script.$inlineScript;
    }

    /**
     * Fix Star Rating CSS class prefix in the Elementor editor.
     *
     * @filter site-reviews/defaults/star-rating/defaults
     */
    public function filterElementorStarRatingDefaults(array $defaults): array
    {
        if ('elementor' === filter_input(INPUT_GET, 'action')) {
            $defaults['prefix'] = 'glsr-';
        }
        return $defaults;
    }

    /**
     * @filter site-reviews/schema/generate
     */
    public function filterGeneratedSchema(array $schema): array
    {
        return empty($schema)
            ? glsr(SchemaParser::class)->generate()
            : $schema;
    }

    /**
     * @param \Elementor\Core\Files\CSS\Post $cssFile
     * @param \Elementor\Element_Base        $element
     *
     * @action elementor/element/parse_css
     */
    public function parseElementCss($cssFile, $element): void
    {
        $shortcode = $element->get_name();
        $shortcodes = [
            'site_review',
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ];
        if (!in_array($shortcode, $shortcodes)) {
            return;
        }
        $ratingColor = $element->get_settings('rating_color') ?: ($element->get_settings('__globals__')['rating_color'] ?? '');
        if (empty($ratingColor)) {
            return;
        }
        $selector = "{$cssFile->get_element_unique_selector($element)} .glsr:not([data-theme])";
        $stylesheet = $cssFile->get_stylesheet();
        $fn = fn ($variable) => [
            'mask-image' => $variable,
            'mask-size' => '100%',
        ];
        $stars = [
            'empty' => 'var(--glsr-star-empty)',
            'error' => 'var(--glsr-star-error)',
            'full' => 'var(--glsr-star-full)',
            'half' => 'var(--glsr-star-half)',
        ];
        if (in_array($shortcode, ['site_review', 'site_reviews'])) {
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-empty", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-full", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-half", $fn($stars['half']));
        } elseif ('site_reviews_form' === $shortcode) {
            $stylesheet->add_rules("{$selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected)", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active", $fn($stars['error']));
        } elseif ('site_reviews_summary' === $shortcode) {
            $stylesheet->add_rules("{$selector} .glsr-star-empty", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-star-full", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-star-half", $fn($stars['half']));
        }
    }

    /**
     * @callback \Elementor\Core\Common\Modules\Ajax\Module::register_ajax_action
     */
    public function queryAssignedPosts(array $data): array
    {
        return [
            'results' => [],
        ];
    }

    /**
     * @callback \Elementor\Core\Common\Modules\Ajax\Module::register_ajax_action
     */
    public function queryAssignedTerms(array $data): array
    {
        return [
            'results' => [],
        ];
    }

    /**
     * @callback \Elementor\Core\Common\Modules\Ajax\Module::register_ajax_action
     */
    public function queryAssignedUsers(array $data): array
    {
        return [
            'results' => [],
        ];
    }

    /**
     * @param \Elementor\Core\Common\Modules\Ajax\Module $manager
     * 
     * @action elementor/ajax/register_actions
     */
    public function registerAjaxActions($manager): void
    {
        $manager->register_ajax_action(glsr()->prefix.'query_assigned_posts', [$this, 'queryAssignedPosts']);
        $manager->register_ajax_action(glsr()->prefix.'query_assigned_terms', [$this, 'queryAssignedTerms']);
        $manager->register_ajax_action(glsr()->prefix.'query_assigned_users', [$this, 'queryAssignedUsers']);
    }

    /**
     * @param \Elementor\Elements_Manager $manager
     *
     * @action elementor/elements/categories_registered
     */
    public function registerElementorCategory($manager): void
    {
        $manager->add_category(glsr()->id, [
            'title' => glsr()->name,
            'icon' => 'eicon-star-o', // default icon
        ]);
    }

    /**
     * @param \Elementor\Widgets_Manager $manager
     *
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets($manager): void
    {
        $manager->register(new ElementorFormWidget());
        $manager->register(new ElementorReviewsWidget());
        $manager->register(new ElementorReviewWidget());
        $manager->register(new ElementorSummaryWidget());
    }

    /**
     * @action admin_enqueue_scripts
     * @action elementor/editor/after_enqueue_styles
     * @action elementor/preview/enqueue_styles
     */
    public function registerInlineStyles(): void
    {
        $iconForm = Helper::svg('assets/images/icons/elementor/icon-form.svg', true);
        $iconReview = Helper::svg('assets/images/icons/elementor/icon-review.svg', true);
        $iconReviews = Helper::svg('assets/images/icons/elementor/icon-reviews.svg', true);
        $iconSummary = Helper::svg('assets/images/icons/elementor/icon-summary.svg', true);
        $css = "
            [class*=\"eicon-glsr-\"]::before {
                background-color: currentColor;
                content: '.';
                display: block;
                mask-repeat: no-repeat;
                mask-size: contain;
                width: 1em;
            }
            .eicon-glsr-form::before {
                mask-image: url(\"{$iconForm}\");
            }
            .eicon-glsr-review::before {
                mask-image: url(\"{$iconReview}\");
            }
            .eicon-glsr-reviews::before {
                mask-image: url(\"{$iconReviews}\");
            }
            .eicon-glsr-summary::before {
                mask-image: url(\"{$iconSummary}\");
            }
        ";
        wp_add_inline_style('elementor-admin', $css);
        wp_add_inline_style('elementor-editor', $css);
        wp_add_inline_style('elementor-frontend', $css."
            [class*=\"eicon-glsr-\"]::before {
                font-size: 28px;
                margin: 0 auto;
            }
        ");
    }

    /**
     * @action elementor/editor/after_enqueue_scripts
     */
    public function registerScripts(): void
    {
        wp_enqueue_script(
            glsr()->id.'/elementor',
            glsr()->url('assets/scripts/integrations/elementor-editor.js'),
            [],
            glsr()->version,
            ['strategy' => 'defer']
        );
        wp_localize_script(glsr()->id.'/elementor', 'GLSR', [
            'action' => glsr()->prefix.'admin_action',
            'nameprefix' => glsr()->id,
            'nonce' => [
                'elementor-assigned_posts' => wp_create_nonce('elementor-assigned_posts'),
                'elementor-assigned_terms' => wp_create_nonce('elementor-assigned_terms'),
                'elementor-assigned_users' => wp_create_nonce('elementor-assigned_users'),
            ],
        ]);
    }

    /**
     * @action site-reviews/route/ajax/elementor-assigned_terms
     */
    public function searchAssignedTerms(Request $request): void
    {
        $search = $request->cast('search', 'string');
        $include = $request->cast('include', 'array');
        $query = stripslashes_deep(sanitize_text_field($search));
        $terms = glsr(Database::class)->terms([
            'number' => 25,
            'search' => $query,
        ]);
        if (!empty($include)) {
            $terms += glsr(Database::class)->terms([
                'term_taxonomy_id' => $include,
            ]);
        }
        $results = [];
        foreach ($terms as $id => $text) {
            $results[] = compact('id', 'text');
        }
        wp_send_json_success($results);
    }
}
