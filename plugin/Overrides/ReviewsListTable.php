<?php

namespace GeminiLabs\SiteReviews\Overrides;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Cast;

class ReviewsListTable extends \WP_Posts_List_Table
{
    /**
     * @param \WP_Post $post
     * @return void
     */
    public function column_cb($post)
    {
        parent::column_cb($post);
        if (!glsr()->can('edit_post', $post->ID) && glsr()->can('respond_to_post', $post->ID)) {
            glsr()->render('partials/screen/locked-indicator');
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     */
    public function column_title($post)
    {
        if (glsr()->can('respond_to_post', $post->ID)) {
            $this->renderInlineData($post);
            $this->renderLockedInfo($post);
        }
        parent::column_title($post);
    }

    /**
     * @return void
     */
    public function inline_edit()
    {
        glsr()->render('partials/screen/inline-edit', [
            'columns' => $this->get_column_count(),
            'screenId' => esc_attr($this->screen->id),
        ]);
    }

    /**
     * @return void
     */
    protected function renderInlineData(\WP_Post $post)
    {
        $response = Cast::toString(glsr(Database::class)->meta($post->ID, 'response'));
        glsr()->render('partials/screen/inline-data', [
            'content' => esc_textarea(trim($post->post_content)),
            'postId' => $post->ID,
            'response' => esc_textarea(trim($response)),
        ]);
    }

    /**
     * @return void
     */
    protected function renderLockedInfo(\WP_Post $post)
    {
        if ('trash' !== $post->post_status) {
            $lockHolder = wp_check_post_lock($post->ID);
            if (false !== $lockHolder) {
                $lockHolder = get_userdata($lockHolder);
                $lockedAvatar = get_avatar($lockHolder->ID, 18);
                $lockedText = esc_html(sprintf(_x('%s is currently editing', 'admin-text', 'site-reviews'), $lockHolder->display_name));
            } else {
                $lockedAvatar = '';
                $lockedText = '';
            }
            glsr()->render('partials/screen/locked-info', [
                'lockedAvatar' => $lockedAvatar,
                'lockedText' => $lockedText,
            ]);
        }
    }
}
