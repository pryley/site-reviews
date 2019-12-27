<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
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
     * @return string
     */
    public function build(array $args = [])
    {
        $this->args = $this->normalize($args);
        if ($this->args['total'] < 2) {
            return '';
        }
        return glsr(Template::class)->build('templates/pagination', [
            'context' => [
                'links' => apply_filters('site-reviews/paginate_links', $this->buildLinks(), $this->args),
                'loader' => '<div class="glsr-loader"></div>',
                'screen_reader_text' => __('Site Reviews navigation', 'site-reviews'),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function buildLinks()
    {
        $args = glsr(Style::class)->paginationArgs($this->args);
        if ('array' == $args['type']) {
            $args['type'] = 'plain';
        }
        return paginate_links($args);
    }

    /**
     * @return array
     */
    protected function normalize(array $args)
    {
        if ($baseUrl = Arr::get($args, 'baseUrl')) {
            $args['base'] = $baseUrl.'%_%';
        }
        return wp_parse_args(array_filter($args), [
            'current' => glsr(QueryBuilder::class)->getPaged(),
            'total' => 1,
        ]);
    }
}
