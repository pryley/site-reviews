<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class Pagination implements PartialContract
{
    /**
     * @var array
     */
    protected $args;

    /**
     * {@inheritdoc}
     */
    public function build(array $args = [])
    {
        $this->args = $this->normalize($args);
        if ($this->args['total'] > 1) { // total pages
            return 'loadmore' === $this->args['type']
                ? $this->buildLoadMoreButton()
                : $this->buildPagination();
        }
    }

    /**
     * @return string|void
     */
    protected function buildLoadMoreButton()
    {
        if ($this->args['total'] > $this->args['current']) {
            return glsr(Template::class)->build('templates/load-more-button', [
                'context' => [
                    'loading_text' => __('Loading, please wait...', 'site-reviews'),
                    'page' => $this->args['current'] + 1,
                    'screen_reader_text' => _x('Load more reviews', 'screen reader text', 'site-reviews'),
                    'text' => __('Load more', 'site-reviews'),
                ],
            ]);
        }
    }

    /**
     * @return string
     */
    protected function buildPagination()
    {
        return glsr(Template::class)->build('templates/pagination', [
            'context' => [
                'links' => glsr()->filterString('paginate_links', $this->paginatedLinks(), $this->args),
                'loader' => '<div class="glsr-loader"><div class="glsr-spinner"></div></div>',
                'screen_reader_text' => _x('Site Reviews navigation', 'screen reader text', 'site-reviews'),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function paginatedLinks()
    {
        $links = (array) paginate_links(wp_parse_args(['type' => 'array'], $this->args));
        $pattern = '/href=["\']([^"\']*?)["\']/i';
        foreach ($links as &$link) {
            if (preg_match($pattern, $link, $matches)) {
                $hrefTag = Arr::get($matches, 0);
                $hrefUrl = Arr::get($matches, 1);
                $page = Helper::getPageNumber($hrefUrl);
                $replacement = sprintf('%s data-page="%d"', $hrefTag, $page);
                $link = str_replace($hrefTag, $replacement, $link);
            }
        }
        return implode("\n", $links);
    }

    /**
     * @return array
     */
    protected function normalize(array $args)
    {
        if ($baseUrl = Arr::get($args, 'baseUrl')) {
            $args['base'] = $baseUrl.'%_%';
        }
        $args = wp_parse_args(array_filter($args), [
            'current' => Helper::getPageNumber(),
            'total' => 1,
        ]);
        return glsr(Style::class)->paginationArgs($args);
    }
}
