<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Url;

/**
 * @property int    $display
 * @property int    $page
 * @property string $pageUrl
 * @property array  $pageUrlParameters
 * @property int    $per_page
 */
class NormalizePaginationArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);
        $this->normalizePage();
        $this->normalizePageUrl();
        $this->normalizePageUrlParameters();
        $this->normalizePerPage();
    }

    /**
     * Returns the paged AJAX request, empty on a page render.
     */
    public static function pagedRequest(): Arguments
    {
        return glsr()->args(glsr()->retrieve(glsr()->paged_handle));
    }

    /**
     * Set the current page number.
     */
    protected function normalizePage(): void
    {
        $args = static::pagedRequest();
        $page = $args->cast('page', 'int', 0);
        $this->page = $page ?: Helper::getPageNumber($args->url, $this->page);
    }

    /**
     * Set the current page URL with the query string removed.
     */
    protected function normalizePageUrl(): void
    {
        $args = static::pagedRequest();
        if (!$args->isEmpty()) {
            $urlPath = Url::path((string) $args->url); // url comes from the request and can be missing
            if (Url::path(Url::home()) === $urlPath) {
                $urlPath = ''; // the home page: Url::home('') is Url::home()
            }
            $this->pageUrl = Url::home($urlPath);
        } elseif (empty($this->pageUrl = get_permalink())) {
            $this->pageUrl = Url::home(Url::path($_SERVER['REQUEST_URI']));
        }
    }

    /**
     * Set the query string of the current page URL.
     */
    protected function normalizePageUrlParameters(): void
    {
        $args = static::pagedRequest();
        $parameters = Url::queries($args->url);
        unset($parameters[glsr()->constant('PAGED_QUERY_VAR')]);
        $this->pageUrlParameters = $parameters;
    }

    /**
     * Set the number of results per page.
     */
    protected function normalizePerPage(): void
    {
        $this->per_page ??= $this->display ??= 10;
    }
}
