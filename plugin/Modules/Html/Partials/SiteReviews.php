<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Backtrace;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviews
{
    /**
     * @var array
     */
    public $args;

    /**
     * @return ReviewsHtml
     */
    public function build(array $args = [])
    {
        $this->args = glsr(SiteReviewsDefaults::class)->unguardedMerge($args);
        $reviews = glsr(ReviewManager::class)->reviews($this->args);
        $this->generateSchema($reviews);
        return new ReviewsHtml($reviews);
    }

    /**
     * @return void
     */
    public function generateSchema(Reviews $reviews)
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->build($this->args, $reviews)
            );
        }
    }
}
