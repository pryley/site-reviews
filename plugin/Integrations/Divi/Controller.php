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
     * Fix compatibility with the Divi Dynamic CSS option.
     *
     * @param array  $shortcodes
     * @param string $content
     *
     * @filter et_dynamic_assets_modules_atf
     */
    public function filterDynamicAssets($shortcodes, $content): array
    {
        if (1 === preg_match('/site_reviews/', Cast::toString($content))) {
            add_filter('et_required_module_assets', function ($assets) {
                $assets[] = 'et_pb_contact_form';
                $assets[] = 'et_pb_gallery';
                $assets[] = 'et_pb_search';
                return array_values(array_unique($assets));
            });
        }
        return Arr::consolidate($shortcodes);
    }

    /**
     * @see filterPaginationLinks
     *
     * @filter site-reviews/paginate_link
     */
    public function filterPaginationLink(array $link, array $args, BuilderContract $builder): array
    {
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
        $link['link'] = $builder->li([
            'text' => $builder->a($args),
            'class' => $class ?? 'page',
        ]);
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
        $links = wp_list_pluck($links, 'link');
        return implode("\n", $links);
    }

    /**
     * @action divi_extensions_init
     */
    public function registerDiviModules(): void
    {
        // new DiviFormWidget();
        // new DiviReviewsWidget();
        // new DiviSummaryWidget();
    }
}
