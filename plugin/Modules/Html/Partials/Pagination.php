<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
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
        if ($this->args['total'] < 2) {
            return;
        }
        if ('loadmore' === $this->args['type']) {
            return $this->buildLoadMoreButton();
        }
        return $this->buildPagination();
    }

    /**
     * @return string
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
    protected function paginatedFauxLinks()
    {
        $links = (array) paginate_links(wp_parse_args(['type' => 'array'], $this->args));
        $pattern = '/(href=["\'])([^"\']*?)(["\'])/i';
        foreach ($links as &$link) {
            if (preg_match($pattern, $link, $matches)) {
                $page = Helper::getPageNumber(Arr::get($matches, 2));
                $replacement = sprintf('data-page="%d" href="#"', $page);
                $link = str_replace(Arr::get($matches, 0), $replacement, $link);
            }
        }
        return implode("\n", $links);
    }

    /**
     * @return string
     */
    protected function paginatedLinks()
    {
        $useUrlParams = glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter');
        return ($useUrlParams || $this->args['type'] !== 'ajax')
            ? paginate_links(wp_parse_args(['type' => 'plain'], $this->args))
            : $this->paginatedFauxLinks();
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
