<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\ImportReviewsCleanup;
use GeminiLabs\SiteReviews\Modules\Queue;

class ImportProductReviewsCleanup extends ImportReviewsCleanup
{
    public function handle(): void
    {
        wp_cache_flush();
        if (0 < $this->imported) {
            glsr(Queue::class)->async('queue/recalculate-meta');
        }
        $this->notices();
    }
}
