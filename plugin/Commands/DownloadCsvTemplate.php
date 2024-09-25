<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\CannotInsertRecord;
use GeminiLabs\League\Csv\EscapeFormula;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rating;

class DownloadCsvTemplate extends AbstractCommand
{
    public function columns(): array
    {
        return [
            'assigned_posts' => _x('The Posts that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'),
            'assigned_terms' => _x('The Categories that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'),
            'assigned_users' => _x('The Users that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'),
            'author_id' => _x('The User ID of the reviewer', 'admin-text', 'site-reviews'),
            'avatar' => _x('The avatar URL of the reviewer', 'admin-text', 'site-reviews'),
            'content' => _x('The review', 'admin-text', 'site-reviews'),
            'date' => _x('The review date', 'admin-text', 'site-reviews'),
            'date_gmt' => _x('The review GMT date', 'admin-text', 'site-reviews'),
            'email' => _x('The reviewer\'s email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('The IP address of the reviewer', 'admin-text', 'site-reviews'),
            'images' => sprintf('%s<br><span class="glsr-notice-inline is-warning">%s</span>',
                _x('The URLs of the review images (separate multiple URLs with a pipe "|" character)', 'admin-text', 'site-reviews'),
                sprintf(_x('%s addon required.', 'the plugin name (admin-text)', 'site-reviews'), '<a href="https://niftyplugins.com/plugins/site-reviews-images/" target="_blank">Review Images</a>')
            ),
            'is_approved' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
            'is_pinned' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
            'is_verified' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
            'name' => _x('The reviewer\'s name', 'admin-text', 'site-reviews'),
            'rating' => sprintf(_x('A number from %d-%d', 'admin-text', 'site-reviews'), glsr()->constant('MIN_RATING', Rating::class), glsr()->constant('MAX_RATING', Rating::class)),
            'response' => _x('The review response', 'admin-text', 'site-reviews'),
            'score' => _x('A positive or negative whole number', 'admin-text', 'site-reviews'),
            'terms' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
            'title' => _x('The title of the review', 'admin-text', 'site-reviews'),
        ];
    }

    public function data(): array
    {
        return [ // order is intentional
            'date' => '2023-01-13 12:01:13',
            'date_gmt' => '',
            'rating' => 5,
            'title' => 'Eclectic, Cozy, and Highly Recommended!',
            'content' => 'I will definitely stay here again. It was a wonderful experience!',
            'name' => 'Matt Mullenweg',
            'email' => 'matt@wordpress.org',
            'avatar' => 'https://gravatar.com/avatar/767fc9c115a1b989744c755db47feb60?s=128',
            'ip_address' => '198.143.164.252',
            'terms' => 1,
            'author_id' => '',
            'is_approved' => 1,
            'is_pinned' => 0,
            'is_verified' => 0,
            'response' => '',
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'score' => 0,
        ];
    }

    public function handle(): void
    {
        try {
            $writer = Writer::createFromString('');
            $writer->addFormatter(new EscapeFormula());
            $writer->insertOne(array_keys($this->data()));
            $writer->insertOne(array_values($this->data()));
            nocache_headers();
            $writer->output('reviews-template.csv');
            exit;
        } catch (CannotInsertRecord $e) {
            $this->fail();
            glsr(Notice::class)->addError($e->getMessage());
            glsr_log()
                ->warning('Unable to insert row into CSV template file')
                ->debug($e->getRecord());
        }
    }

    public function required(): array
    {
        return [
            'date', 'rating',
        ];
    }

    public function tableData(): array
    {
        $data = [];
        foreach ($this->columns() as $name => $description) {
            $required = in_array($name, $this->required())
                ? sprintf('<span class="glsr-tag glsr-tag-required">%s</span>', _x('Yes', 'admin-text', 'site-reviews'))
                : sprintf('<span class="glsr-tag">%s</span>', _x('No', 'admin-text', 'site-reviews'));
            $data[] = compact('description', 'name', 'required');
        }
        return $data;
    }
}
