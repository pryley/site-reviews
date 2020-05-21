<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
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
     * @inheritDoc
     */
    public function build(array $args = [])
    {
        $this->args = $this->normalize($args);
        if ($this->args['total'] < 2) {
            return '';
        }
        return glsr(Template::class)->build('templates/pagination', [
            'context' => [
                'links' => glsr()->filterString('paginate_links', $this->buildLinks(), $this->args),
                'loader' => '<div class="glsr-loader"></div>',
                'screen_reader_text' => __('Site Reviews navigation', 'site-reviews'),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function buildFauxLinks()
    {
        $links = (array) paginate_links(wp_parse_args(['type' => 'array'], $this->args));
        $pattern = '/(href=["\'])([^"\']*?)(["\'])/i';
        foreach ($links as &$link) {
            if (!preg_match($pattern, $link, $matches)) {
                continue;
            }
            parse_str(parse_url(Arr::get($matches, 2), PHP_URL_QUERY), $urlQuery);
            $page = (int) Arr::get($urlQuery, glsr()->constant('PAGED_QUERY_VAR'), 1);
            $replacement = sprintf('data-page="%d" href="#"', $page);
            $link = str_replace(Arr::get($matches, 0), $replacement, $link);
        }
        return implode("\n", $links);
    }

    /**
     * @return string
     */
    protected function buildLinks()
    {
        return glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter')
            ? paginate_links(wp_parse_args(['type' => 'plain'], $this->args))
            : $this->buildFauxLinks();
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
            'current' => glsr(QueryBuilder::class)->getPaged(),
            'total' => 1,
        ]);
        return glsr(Style::class)->paginationArgs($args);
    }
}
