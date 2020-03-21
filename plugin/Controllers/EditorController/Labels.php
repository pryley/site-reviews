<?php

namespace GeminiLabs\SiteReviews\Controllers\EditorController;

use GeminiLabs\SiteReviews\Application;
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
        global $wp_scripts;
        $strings = [
            'savePending' => __('Save as Unapproved', 'site-reviews'),
            'published' => __('Approved', 'site-reviews'),
        ];
        if (isset($wp_scripts->registered['post']->extra['data'])) {
            $l10n = &$wp_scripts->registered['post']->extra['data'];
            foreach ($strings as $search => $replace) {
                $l10n = preg_replace('/("'.$search.'":")([^"]+)/u', '$1'.$replace, $l10n);
            }
        }
    }

    /**
     * @return array
     */
    protected function getReviewLabels()
    {
        return [
            'approved' => __('Review has been approved and published.', 'site-reviews'),
            'draft_updated' => __('Review draft updated.', 'site-reviews'),
            'preview' => __('Preview review', 'site-reviews'),
            'published' => __('Review approved and published.', 'site-reviews'),
            'restored' => __('Review restored to revision from %s.', 'site-reviews'),
            'reverted' => __('Review has been reverted to its original submission state (title, content, and submission date).', 'site-reviews'),
            'saved' => __('Review saved.', 'site-reviews'),
            'scheduled' => __('Review scheduled for: %s.', 'site-reviews'),
            'submitted' => __('Review submitted.', 'site-reviews'),
            'unapproved' => __('Review has been unapproved and is now pending.', 'site-reviews'),
            'updated' => __('Review updated.', 'site-reviews'),
            'view' => __('View review', 'site-reviews'),
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
                'Pending' => __('Unapproved', 'site-reviews'),
                'Pending Review' => __('Unapproved', 'site-reviews'),
                'Privately Published' => __('Privately Approved', 'site-reviews'),
                'Publish' => __('Approve', 'site-reviews'),
                'Published' => __('Approved', 'site-reviews'),
                'Save as Pending' => __('Save as Unapproved', 'site-reviews'),
            ];
        }
        return $labels;
    }
}
