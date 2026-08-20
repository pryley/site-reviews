<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FetchPagedReviews extends AbstractCommand
{
    public $request;

    protected ?ReviewsHtml $html = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(): void
    {
        glsr()->store(glsr()->paged_handle, $this->request->toArray());
        $this->html = glsr(SiteReviewsShortcode::class)
            ->normalize($this->request->cast('atts', 'array'))
            ->buildReviewsHtml();
    }

    public function response(): array
    {
        if (is_null($this->html)) {
            return [];
        }
        return [
            'max_num_pages' => $this->html->max_num_pages,
            'pagination' => $this->html->getPagination($wrap = false),
            'reviews' => $this->html->getReviews(),
        ];
    }
}
