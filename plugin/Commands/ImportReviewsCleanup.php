<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ImportManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Request;

class ImportReviewsCleanup extends AbstractCommand
{
    protected int $duplicates = 0;
    /** @var string[] */
    protected array $errors = [];
    protected int $failed = 0;
    protected int $imported = 0;
    protected int $skipped = 0;

    public function __construct(Request $request)
    {
        $this->duplicates = $request->cast('duplicates', 'int');
        $this->errors = $request->cast('errors', 'array');
        $this->failed = $request->cast('failed', 'int');
        $this->imported = $request->cast('imported', 'int');
        $this->skipped = $request->cast('skipped', 'int');
    }

    public function handle(): void
    {
        glsr(ImportManager::class)->unlock(); // release even when nothing was imported
        wp_cache_flush();
        if (0 < $this->imported) {
            glsr(ImportManager::class)->flush(); // drop the temporary table in the database
            glsr(ImportManager::class)->unlinkTempFile(); //.delete the temporary import file if it exists
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

    protected function duplicatesDetail(): string
    {
        $detail = sprintf(
            /* translators: %s: number of skipped entries */
            _nx('%s entry was skipped because a review with the same details already exists.', '%s entries were skipped because a review with the same details already exists.', $this->duplicates, 'admin-text', 'site-reviews'),
            number_format_i18n($this->duplicates)
        );
        $detail .= ' '._x('A review in the Trash also counts as an existing review.', 'admin-text', 'site-reviews');
        $trashed = Cast::toInt(wp_count_posts(glsr()->post_type)->trash ?? 0); // the count comes back as a string
        if (0 < $trashed) {
            $link = glsr(Builder::class)->a([
                'href' => add_query_arg('post_status', 'trash', glsr_admin_url()),
                'text' => _x('Trash', 'admin-text', 'site-reviews'),
            ]);
            $detail .= ' '.sprintf(
                /* translators: %1$s: number of reviews, %2$s: link to the Trash */
                _nx('%1$s review is in the %2$s. Empty the Trash to import it again.', '%1$s reviews are in the %2$s. Empty the Trash to import them again.', $trashed, 'admin-text', 'site-reviews'),
                number_format_i18n($trashed),
                $link
            );
        }
        return $detail;
    }

    protected function failedDetail(): string
    {
        $detail = sprintf(
            /* translators: %s: number of skipped entries */
            _nx('%s entry could not be saved as a review.', '%s entries could not be saved as reviews.', $this->failed, 'admin-text', 'site-reviews'),
            number_format_i18n($this->failed)
        );
        return $detail.' '.sprintf(
            /* translators: %s: link to the Console page */
            _x('Check the %s page for the reason.', 'admin-text', 'site-reviews'),
            glsr_admin_link('tools.console')
        );
    }

    protected function notices(): void
    {
        $notice = sprintf(
            /* translators: %s: number of imported reviews */
            _nx('%s review was imported.', '%s reviews were imported.', $this->imported, 'admin-text', 'site-reviews'),
            number_format_i18n($this->imported)
        );
        if (0 === $this->skipped) {
            glsr(Notice::class)->addSuccess($notice);
            return;
        }
        $skipped = sprintf(
            /* translators: %s: number of skipped entries */
            _nx('%s entry was skipped.', '%s entries were skipped.', $this->skipped, 'admin-text', 'site-reviews'),
            number_format_i18n($this->skipped)
        );
        $notice = sprintf('<strong>%s</strong> %s', $notice, $skipped);
        $details = [];
        if (!empty($this->errors)) {
            natsort($this->errors);
            /* translators: %s: list of warning messages */
            $errorDetail = _x('One or more warnings were triggered during import: %s', 'admin-text', 'site-reviews');
            $errors = array_map(fn ($error) => "<mark>{$error}</mark>", $this->errors);
            $errors = sprintf($errorDetail, Str::naturalJoin($errors));
            glsr_log()->warning($this->errors);
            $details[] = $errors;
        }
        if (0 < $this->duplicates) {
            $details[] = $this->duplicatesDetail();
        }
        if (0 < $this->failed) {
            $details[] = $this->failedDetail();
        }
        glsr(Notice::class)->addWarning($notice, $details);
    }
}
