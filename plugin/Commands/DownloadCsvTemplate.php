<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\CannotInsertRecord;
use GeminiLabs\League\Csv\EscapeFormula;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rating;

class DownloadCsvTemplate extends AbstractCommand
{
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

    public function tableColumns(): array
    {
        return [
            'default' => [
                'assigned_posts' => _x('The Post ID or "post_type:slug" of the page that the review is assigned to (separate multiple values with a comma)', 'admin-text', 'site-reviews'),
                'assigned_terms' => _x('The Term ID or "slug" of the category that the review is assigned to (separate multiple values with a comma)', 'admin-text', 'site-reviews'),
                'assigned_users' => _x('The User ID or "username" of the user that the review is assigned to (separate multiple values with a comma)', 'admin-text', 'site-reviews'),
                'author_id' => _x('The User ID or "username" of the reviewer', 'admin-text', 'site-reviews'),
                'avatar' => _x('The avatar URL of the reviewer', 'admin-text', 'site-reviews'),
                'content' => _x('The review text', 'admin-text', 'site-reviews'),
                'date' => _x('The review date', 'admin-text', 'site-reviews'),
                'date_gmt' => _x('The review GMT date', 'admin-text', 'site-reviews'),
                'email' => _x('The reviewer\'s email', 'admin-text', 'site-reviews'),
                'geolocation_city' => _x('The city name', 'admin-text', 'site-reviews'),
                'geolocation_continent' => _x('The two-letter continent code', 'admin-text', 'site-reviews'),
                'geolocation_country' => _x('The two-letter country code (ISO 3166-1 alpha-2)', 'admin-text', 'site-reviews'),
                'geolocation_region' => _x('The region/state short code (FIPS or ISO)', 'admin-text', 'site-reviews'),
                'ip_address' => _x('The IP address of the reviewer', 'admin-text', 'site-reviews'),
                'is_approved' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
                'is_pinned' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
                'is_verified' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
                'name' => _x('The reviewer\'s name', 'admin-text', 'site-reviews'),
                'rating' => sprintf(_x('A number from %d-%d', 'admin-text', 'site-reviews'), Rating::min(), Rating::max()),
                'response' => _x('The review response', 'admin-text', 'site-reviews'),
                'terms' => sprintf(_x('%s or %s', 'admin-text', 'site-reviews'), 'TRUE', 'FALSE'),
                'title' => _x('The title of the review', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-actions' => [
                'language' => sprintf(_x('The ISO 639-1 language code of the review. See %s for a list of all supported languages.', 'admin-text', 'site-reviews'), '<a href="https://developers.deepl.com/docs/getting-started/supported-languages#translation-source-languages" target="_blank">DeepL</a>'),
                'score' => _x('The number of times the review was upvoted.', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-forms' => [
                'custom_*' => _x('The value of a custom review field (replace the asterisk <code>*</code> of the column name with the name of your custom field).', 'admin-text', 'site-reviews'),
                'form' => _x('The Post ID or slug of the Review Form used to submit the review', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-images' => [
                'images' => _x('The URLs of the review images (separate multiple URLs with a pipe "|" character or comma)', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-themes' => [
                'type' => _x('The lowercase name of the platform that the review was exported from (only use this if the review was exported from another review platform, i.e. google, tripadvisor, etc.)', 'admin-text', 'site-reviews'),
                'url' => _x('The review URL (only use this if the review was exported from another review platform, i.e. google, tripadvisor, etc.)', 'admin-text', 'site-reviews'),
            ],
        ];
    }

    public function tableData(): array
    {
        $data = [];
        foreach ($this->tableColumns() as $group => $columns) {
            foreach ($columns as $name => $description) {
                $required = in_array($name, $this->required())
                    ? sprintf('<span class="glsr-tag glsr-tag-required">%s</span>', _x('Yes', 'admin-text', 'site-reviews'))
                    : sprintf('<span class="glsr-tag">%s</span>', _x('No', 'admin-text', 'site-reviews'));
                $notice = '';
                if ('default' !== $group) {
                    $text = _x('%s addon required.', 'link to addon page (admin-text)', 'site-reviews');
                    $notice = sprintf('<div class="glsr-notice-inline components-notice is-warning">%s</div>',
                        sprintf($text, glsr_premium_link($group))
                    );
                }
                $data[$name] = compact('description', 'notice', 'required');
            }
        }
        ksort($data);
        return $data;
    }
}
