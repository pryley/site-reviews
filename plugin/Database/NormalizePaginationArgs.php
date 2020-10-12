<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;

/**
 * @property int $page;
 * @property string $pageUrl;
 * @property array $pageUrlParameters;
 */
class NormalizePaginationArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);
        $this->normalizePage();
        $this->normalizePageUrl();
        $this->normalizePageUrlParameters();
    }

    /**
     * @return void
     */
    protected function normalizePage()
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $page = $args->get('page', 0);
        $this->page = $page
            ? $page
            : Helper::getPageNumber($args->url);
    }

    /**
     * @return void
     */
    protected function normalizePageUrl()
    {
        if ($request = glsr()->retrieve(glsr()->paged_handle)) {
            $urlPath = Url::path($request->url);
            $this->pageUrl = Url::path(home_url()) === $urlPath
                ? Url::home()
                : Url::home($urlPath);
        } else {
            $this->pageUrl = filter_input(INPUT_SERVER, 'REQUEST_URI');
        }
    }

    /**
     * @return void
     */
    protected function normalizePageUrlParameters()
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $parameters = Url::queries($args->url);
        unset($parameters[glsr()->constant('PAGED_QUERY_VAR')]);
        $this->pageUrlParameters = $parameters;
    }
}
