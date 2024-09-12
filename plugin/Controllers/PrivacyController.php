<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;

class PrivacyController extends AbstractController
{
    protected $itemsRemoved;
    protected $itemsRetained;
    protected $messages;
    protected $perPage;

    public function __construct()
    {
        $this->itemsRemoved = false;
        $this->itemsRetained = false;
        $this->messages = [];
        $this->perPage = 100;
        if (!glsr()->filterBool('personal-data/erase-all', true)) {
            $this->itemsRetained = true;
            $this->messages[] = _x('The email and associated name and IP address has been removed from all reviews, but the reviews themselves were not removed.', 'admin-text', 'site-reviews');
        }
    }

    /**
     * @see filterPersonalDataErasers
     */
    public function erasePersonalDataCallback(string $email, int $page = 1): array
    {
        $reviews = $this->reviews($email, $page);
        array_walk($reviews, [$this, 'erasePersonalData']);
        return [
            'done' => count($reviews) < $this->perPage,
            'items_removed' => $this->itemsRemoved,
            'items_retained' => $this->itemsRetained,
            'messages' => $this->messages,
        ];
    }

    /**
     * @see filterPersonalDataExporters
     */
    public function exportPersonalDataCallback(string $email, int $page = 1): array
    {
        $reviews = $this->reviews($email, $page);
        $data = array_map([$this, 'exportPersonalData'], $reviews);
        return [
            'data' => $data,
            'done' => count($reviews) < $this->perPage,
        ];
    }

    /**
     * @filter wp_privacy_personal_data_erasers
     */
    public function filterPersonalDataErasers(array $erasers): array
    {
        $erasers[glsr()->id] = [
            'callback' => [$this, 'erasePersonalDataCallback'],
            'eraser_friendly_name' => glsr()->name,
        ];
        return $erasers;
    }

    /**
     * @filter wp_privacy_personal_data_exporters
     */
    public function filterPersonalDataExporters(array $exporters): array
    {
        $exporters[glsr()->id] = [
            'callback' => [$this, 'exportPersonalDataCallback'],
            'exporter_friendly_name' => glsr()->name,
        ];
        return $exporters;
    }

    /**
     * @action admin_init
     */
    public function privacyPolicyContent(): void
    {
        $content = glsr()->build('partials/privacy-policy');
        wp_add_privacy_policy_content(glsr()->name, wp_kses_post($content));
    }

    protected function erasePersonalData(Review $review): void
    {
        glsr()->action('personal-data/erase', $review, $this->itemsRetained);
        if (!$this->itemsRetained) {
            wp_delete_post($review->ID, true);
        } else {
            glsr(ReviewManager::class)->deleteRevisions($review->ID);
            glsr(ReviewManager::class)->updateRating($review->ID, [
                'email' => '',
                'ip_address' => '',
                'name' => '',
            ]);
            delete_post_meta($review->ID, '_submitted'); // delete the original stored request
            delete_post_meta($review->ID, '_submitted_hash');
        }
        $this->itemsRemoved = true;
    }

    protected function exportPersonalData(Review $review): array
    {
        $data = [];
        $fields = [ // order is intentional
            'title' => _x('Review Title', 'admin-text', 'site-reviews'),
            'content' => _x('Review Content', 'admin-text', 'site-reviews'),
            'name' => _x('Name', 'admin-text', 'site-reviews'),
            'email' => _x('Email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
            'terms' => _x('Terms Accepted', 'admin-text', 'site-reviews'),
        ];
        foreach ($fields as $field => $name) {
            if ($value = $review->$field) {
                if ('terms' === $field && Cast::toBool($value)) {
                    $value = $review->date;
                }
                $data[] = ['name' => $name, 'value' => $value];
            }
        }
        return [
            'data' => glsr()->filterArray('personal-data/export', $data, $review),
            'group_id' => glsr()->id,
            'group_label' => _x('Reviews', 'admin-text', 'site-reviews'),
            'item_id' => glsr()->post_type."-{$review->ID}",
        ];
    }

    protected function reviews(string $email, int $page): array
    {
        return glsr(Query::class)->reviews([
            'email' => $email,
            'page' => $page,
            'per_page' => $this->perPage,
        ]);
    }
}
