<?php

namespace GeminiLabs\SiteReviews\Overrides;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;

class ReviewsListTable extends \WP_Posts_List_Table
{
    /**
     * @param \WP_Post $post
     *
     * @return void
     */
    public function column_title($post)
    {
        if (glsr()->can('respond_to_post', $post->ID)) {
            $this->renderInlineData($post);
        }
        ob_start();
        parent::column_title($post);
        $value = ob_get_clean();
        $value = str_replace(
            ['<strong>', '</strong>'],
            ['<div class="review-title"><strong>', '</strong></div>'],
            $value
        );
        echo $value;
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
            'mode' => esc_attr(Str::restrictTo(['excerpt', 'list'], $mode ?? 'list', 'list')),
            'screen_id' => esc_attr($this->screen->id),
            'taxonomy' => get_taxonomy(glsr()->taxonomy),
        ]);
    }

    /**
     * @return void
     */
    public function views()
    {
        echo glsr(Builder::class)->div(glsr(Notice::class)->get(), [
            'id' => 'glsr-notices',
        ]);
        parent::views();
    }

    protected function getAdditionalFieldsets()
    {
        ob_start();
        [$columns] = $this->get_column_info();
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
        $noChange = sprintf('&mdash; %s &mdash;', _x('No Change', 'admin-text', 'site-reviews'));
        return glsr()->build('partials/listtable/filter', [
            'action' => 'filter-author',
            'class' => 'authors',
            'id' => 'post_author',
            'name' => 'post_author',
            'options' => [
                '' => $noChange,
                0 => _x('No Author', 'admin-text', 'site-reviews'),
            ],
            'placeholder' => $noChange,
            'selected' => $noChange,
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
