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
    public function column_title($post)
    {
        if (glsr()->can('respond_to_post', $post->ID)) {
            $this->renderInlineData($post);
        }
        parent::column_title($post);
    }

    /**
     * @return void
     */
    public function inline_edit()
    {
        global $mode;
        glsr()->render('partials/screen/inline-edit', [
            'additional_fieldsets' => $this->getAdditionalFieldsets(),
            'author_dropdown' => $this->getAuthorDropdown(),
            'columns' => $this->get_column_count(),
            'mode' => esc_attr((isset($mode) && 'excerpt' === $mode) ? 'excerpt' : 'list'),
            'screen_id' => esc_attr($this->screen->id),
            'taxonomy' => get_taxonomy(glsr()->taxonomy),
        ]);
    }

    protected function getAdditionalFieldsets()
    {
        ob_start();
        list($columns) = $this->get_column_info();
        $coreColumns = ['author', 'categories', 'cb', 'comments', 'date', 'tags', 'title'];
        foreach ($columns as $columnName => $columnTitle) {
            if (!in_array($columnName, $coreColumns)) {
                do_action('bulk_edit_custom_box', $columnName, glsr()->post_type); // @since WP 2.7.0
            }
        }
        return ob_get_clean();
    }

    /**
     * @return string
     */
    protected function getAuthorDropdown()
    {
        if (!glsr()->can('edit_others_posts')) {
            return '';
        }
        $placeholder = _x('Author Unknown', 'admin-text', 'site-reviews');
        $selected = $placeholder;
        $value = (empty($post->ID) ? get_current_user_id() : $post->post_author);
        if ($user = get_user_by('id', $value)) {
            $selected = $user->display_name;
        }
        return glsr()->build('partials/listtable/filter', [
            'action' => 'filter-author',
            'class' => 'authors',
            'id' => 'post_author',
            'name' => 'post_author',
            'options' => [
                '' => sprintf('&mdash; %s &mdash;', _x('No Change', 'admin-text', 'site-reviews')),
                0 => _x('No Author', 'admin-text', 'site-reviews'),
            ],
            'placeholder' => sprintf('&mdash; %s &mdash;', _x('No Change', 'admin-text', 'site-reviews')),
            'selected' => sprintf('&mdash; %s &mdash;', _x('No Change', 'admin-text', 'site-reviews')),
            'value' => '',
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
}
