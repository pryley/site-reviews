<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Request;

class ImportProductReviewsCleanup extends AbstractCommand
{
    /** @var string[] */
    protected array $errors = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    public function __construct(Request $request)
    {
        $this->errors = $request->cast('errors', 'array');
        $this->imported = $request->cast('imported', 'int');
        $this->skipped = $request->cast('skipped', 'int');
    }

    public function handle(): void
    {
        wp_cache_flush();
        if (0 < $this->imported) {
            glsr(Queue::class)->async('queue/recalculate-meta');
        }
        $this->notices();
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }

    protected function notices(): void
    {
        $notice = sprintf(
            _nx('%s review was imported.', '%s reviews were imported.', $this->imported, 'admin-text', 'site-reviews'),
            number_format_i18n($this->imported)
        );
        if (0 === $this->skipped) {
            glsr(Notice::class)->addSuccess($notice);
            return;
        }
        $skipped = sprintf(
            _nx('%s entry was skipped.', '%s entries were skipped.', $this->skipped, 'admin-text', 'site-reviews'),
            number_format_i18n($this->skipped)
        );
        $notice = sprintf('<strong>%s</strong> %s', $notice, $skipped);
        $details = [];
        if (!empty($this->errors)) {
            natsort($this->errors);
            $errorDetail = _x('One or more warnings were triggered during import: %s', 'admin-text', 'site-reviews');
            $errors = array_map(fn ($error) => "<mark>{$error}</mark>", $this->errors);
            $errors = sprintf($errorDetail, Str::naturalJoin($errors));
            glsr_log()->warning($errors);
            $details[] = $errors;
        }
        glsr(Notice::class)->addWarning($notice, $details);
    }
}
