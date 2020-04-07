<?php

namespace GeminiLabs\SiteReviews\Controllers\EditorController;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use WP_Post;

class Labels
{
    /**
     * @param string $translation
     * @param string $test
     * @return string
     */
    public function filterPostStatusLabels($translation, $text)
    {
        $replacements = $this->getStatusLabels();
        return array_key_exists($text, $replacements)
            ? $replacements[$text]
            : $translation;
    }

    /**
     * @return array
     */
    public function filterUpdateMessages(array $messages)
    {
        $post = get_post();
        if (!($post instanceof WP_Post)) {
            return;
        }
        $strings = $this->getReviewLabels();
        $restored = filter_input(INPUT_GET, 'revision');
        if ($revisionTitle = wp_post_revision_title(intval($restored), false)) {
            $restored = sprintf($strings['restored'], $revisionTitle);
        }
        $scheduled_date = date_i18n('M j, Y @ H:i', strtotime($post->post_date));
        $messages[Application::POST_TYPE] = [
             1 => $strings['updated'],
             4 => $strings['updated'],
             5 => $restored,
             6 => $strings['published'],
             7 => $strings['saved'],
             8 => $strings['submitted'],
             9 => sprintf($strings['scheduled'], '<strong>'.$scheduled_date.'</strong>'),
            10 => $strings['draft_updated'],
            50 => $strings['approved'],
            51 => $strings['unapproved'],
            52 => $strings['reverted'],
        ];
        return $messages;
    }

    /**
     * @return void
     */
    public function translatePostStatusLabels()
    {
        $pattern = '/^([^{]+)(.+)([^}]+)$/';
        $script = Arr::get(wp_scripts(), 'registered.post.extra.data');
        preg_match($pattern, $script, $matches);
        if (4 === count($matches) && $i10n = json_decode($matches[2], JSON_OBJECT_AS_ARRAY)) {
            $i10n['privatelyPublished'] = _x('Privately Approved', 'admin-text', 'site-reviews');
            $i10n['publish'] = _x('Approve', 'admin-text', 'site-reviews');
            $i10n['published'] = _x('Approved', 'admin-text', 'site-reviews');
            $i10n['publishOn'] = _x('Approve on:', 'admin-text', 'site-reviews');
            $i10n['publishOnPast'] = _x('Approved on:', 'admin-text', 'site-reviews');
            $i10n['savePending'] = _x('Save as Unapproved', 'admin-text', 'site-reviews');
            $script = $matches[1].json_encode($i10n).$matches[3];
            Arr::set(wp_scripts(), 'registered.post.extra.data', $script);
        }
    }

    /**
     * @return array
     */
    protected function getReviewLabels()
    {
        return [
            'approved' => _x('Review has been approved and published.', 'admin-text', 'site-reviews'),
            'draft_updated' => _x('Review draft updated.', 'admin-text', 'site-reviews'),
            'preview' => _x('Preview review', 'admin-text', 'site-reviews'),
            'published' => _x('Review approved and published.', 'admin-text', 'site-reviews'),
            'restored' => _x('Review restored to revision from %s.', 'admin-text', 'site-reviews'),
            'reverted' => _x('Review has been reverted to its original submission state (title, content, and submission date).', 'admin-text', 'site-reviews'),
            'saved' => _x('Review saved.', 'admin-text', 'site-reviews'),
            'scheduled' => _x('Review scheduled for: %s.', 'admin-text', 'site-reviews'),
            'submitted' => _x('Review submitted.', 'admin-text', 'site-reviews'),
            'unapproved' => _x('Review has been unapproved and is now pending.', 'admin-text', 'site-reviews'),
            'updated' => _x('Review updated.', 'admin-text', 'site-reviews'),
            'view' => _x('View review', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * Store the labels to avoid unnecessary loops.
     * @return array
     */
    protected function getStatusLabels()
    {
        static $labels;
        if (empty($labels)) {
            $labels = [
                'Pending' => _x('Unapproved', 'admin-text', 'site-reviews'),
                'Pending Review' => _x('Unapproved', 'admin-text', 'site-reviews'),
                'Privately Published' => _x('Privately Approved', 'admin-text', 'site-reviews'),
                'Publish' => _x('Approve', 'admin-text', 'site-reviews'),
                'Published' => _x('Approved', 'admin-text', 'site-reviews'),
                'Save as Pending' => _x('Save as Unapproved', 'admin-text', 'site-reviews'),
            ];
        }
        return $labels;
    }
}
