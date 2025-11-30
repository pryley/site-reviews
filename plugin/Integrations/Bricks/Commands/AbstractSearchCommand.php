<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Integrations\Bricks\Concerns\ManagesBricksAjax;

abstract class AbstractSearchCommand extends AbstractCommand
{
    use ManagesBricksAjax;

    public array $include;
    public string $search;

    protected array $response = [];

    public function __construct()
    {
        $rawInclude = filter_input(\INPUT_GET, 'include', \FILTER_DEFAULT, \FILTER_FORCE_ARRAY);
        $rawSearch = filter_input(\INPUT_GET, 'search', \FILTER_SANITIZE_STRING);
        $this->include = $rawInclude ?? [];
        $this->search = stripslashes_deep(sanitize_text_field($rawSearch ?: ''));
    }

    public function response(): array
    {
        return $this->response;
    }
}
