<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Paginate;

class Controller extends AbstractController
{
    /**
     * @action wp_enqueue_scripts
     */
    public function enqueueNextAssets(): void
    {
        if (!function_exists('et_builder_d5_enabled') || !et_builder_d5_enabled()) {
            return;
        }
        wp_register_style(
            glsr()->id.'/divi/builder-bundle',
            glsr()->url('assets/divi/styles/bundle.css'),
            [],
            glsr()->version
        );
        wp_enqueue_style(glsr()->id.'/divi/builder-bundle');
    }

    /**
     * @action divi_visual_builder_assets_before_enqueue_scripts
     */
    public function enqueueNextBundledAssets(): void
    {
        if (!function_exists('et_builder_d5_enabled') || !et_builder_d5_enabled() || !et_core_is_fb_enabled()) {
            return;
        }
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name' => glsr()->id.'/divi/builder-bundle',
            'version' => glsr()->version,
            'script' => [
                'src' => glsr()->url('assets/divi/scripts/bundle.js'),
                'deps' => [
                    'divi-module-library',
                    'divi-vendor-wp-hooks',
                ],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name' => glsr()->id.'/divi/builder-vb-bundle',
            'version' => glsr()->version,
            'style' => [
                'src' => glsr()->url('assets/divi/styles/vb-bundle.css'),
                'deps' => [],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);
        foreach ([
            'site_review',
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ] as $shortcode) {
            $parts = explode('_', $shortcode);
            $suffix = end($parts);
            $handle = sprintf('%s-%s-style', glsr()->ID, $suffix);
            $registered = wp_styles()->registered[$handle] ?? null;
            if ($src = $registered->src ?? '') {
                \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
                    'name' => $handle,
                    'version' => glsr()->version,
                    'style' => [
                        'src' => $src,
                        'deps' => [],
                        'enqueue_top_window' => false,
                        'enqueue_app_window' => true,
                    ],
                ]);
            }
        }
    }

    /**
     * Fix compatibility with the Divi Dynamic CSS option.
     *
     * @param array  $shortcodes
     * @param string $content
     *
     * @filter et_dynamic_assets_modules_atf
     */
    public function filterDynamicAssets($shortcodes, $content): array
    {
        if ('divi' !== glsr_get_option('general.style')) {
            return $shortcodes;
        }
        if (1 === preg_match('/site_reviews/', Cast::toString($content))) {
            add_filter('et_required_module_assets', function ($assets) {
                $assets[] = 'et_pb_button';
                $assets[] = 'et_pb_contact_form';
                $assets[] = 'et_pb_gallery';
                $assets[] = 'et_pb_search';
                return array_values(array_unique($assets));
            });
        }
        return Arr::consolidate($shortcodes);
    }

    /**
     * @action site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineWooStyles(string $css): string
    {
        $css .= file_get_contents(glsr()->path('assets/styles/integrations/divi-woo-inline.css'));
        return $css;
    }

    /**
     * @param string $content
     *
     * @action divi_frontend_assets_dynamic_assets_required_module_assets
     */
    public function filterNextDynamicAssets(array $assets, $content): array
    {
        if ('divi' !== glsr_get_option('general.style')) {
            return $assets;
        }
        if (1 === preg_match('/wp:glsr-divi\//', Cast::toString($content))) {
            $assets[] = 'divi/button';
            $assets[] = 'divi/gallery';
            $assets[] = 'divi/search';
            return array_values(array_unique($assets));
        }
        return $assets;
    }

    /**
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterNextModalWrappedBy(array $builders): array
    {
        $builders[] = 'divi';
        return $builders;
    }

    /**
     * @see filterPaginationLinks
     *
     * @filter site-reviews/paginate_link
     */
    public function filterPaginationLink(array $link, array $args, BuilderContract $builder): array
    {
        if (empty($link['link'])) {
            return $link;
        }
        $link = wp_parse_args($link, [
            'tag' => 'span',
            'type' => 'page',
        ]);
        $class = 'page';
        if ('current' === $link['type']) {
            $args['class'] = 'active';
        }
        if ('prev' === $link['type']) {
            $args['class'] = 'page-prev';
            $class = 'prev';
        }
        if ('next' === $link['type']) {
            $args['class'] = 'page-next';
            $class = 'next';
        }
        $text = $builder->build($link['tag'], $args);
        $link['link'] = $builder->li(compact('class', 'text'));
        return $link;
    }

    /**
     * @filter site-reviews/paginate_links
     */
    public function filterPaginationLinks(string $links, array $args): string
    {
        if ('divi' !== glsr_get_option('general.style')) {
            return $links;
        }
        $args = wp_parse_args(['end_size' => 0, 'mid_size' => 2], $args);
        add_filter('site-reviews/paginate_link', [$this, 'filterPaginationLink'], 10, 3);
        $links = (new Paginate($args))->links();
        remove_filter('site-reviews/paginate_link', [$this, 'filterPaginationLink']);
        $links = array_filter(wp_list_pluck($links, 'link'));
        return implode("\n", $links);
    }

    /**
     * @param \ET\Builder\Framework\DependencyManagement\DependencyTree $dependencyTree
     *
     * @action divi_module_library_modules_dependency_tree
     */
    public function registerNextModules($dependencyTree): void
    {
        if (!function_exists('et_builder_d5_enabled') || !et_builder_d5_enabled()) {
            return;
        }
        $dependencyTree->add_dependency(new Modules\SiteReview\Module());
        $dependencyTree->add_dependency(new Modules\SiteReviews\Module());
        $dependencyTree->add_dependency(new Modules\SiteReviewsForm\Module());
        $dependencyTree->add_dependency(new Modules\SiteReviewsSummary\Module());
    }
}
