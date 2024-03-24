<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Url;

/**
 * @property int    $page;
 * @property string $pageUrl;
 * @property array  $pageUrlParameters;
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
     * Set the current page number.
     */
    protected function normalizePage(): void
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $page = $args->get('page', 0);
        $this->page = $page
            ? $page
            : Helper::getPageNumber($args->url, $this->page);
    }

    /**
     * Set the current page URL with the query string removed.
     */
    protected function normalizePageUrl(): void
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        if (!$args->isEmpty()) {
            $urlPath = Url::path($args->url);
            $this->pageUrl = Url::path(Url::home()) === $urlPath
                ? Url::home()
                : Url::home($urlPath);
        } elseif (empty($this->pageUrl = get_permalink())) {
            $this->pageUrl = Url::home(Url::path($_SERVER['REQUEST_URI']));
        }
    }

    /**
     * Set the query string of the current page URL.
     */
    protected function normalizePageUrlParameters(): void
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $parameters = Url::queries($args->url);
        unset($parameters[glsr()->constant('PAGED_QUERY_VAR')]);
        $this->pageUrlParameters = $parameters;
    }
}
