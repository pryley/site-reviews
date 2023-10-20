<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Defaults\ToggleStatusDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ToggleStatus implements Contract
{
    public $postId;
    public $review;
    public $status;

    public function __construct(array $input)
    {
        $args = glsr(ToggleStatusDefaults::class)->restrict($input);
        $this->postId = $args['post_id'];
        $this->review = glsr_get_review($args['post_id']);
        $this->status = $args['status'];
    }

    /**
     * @return array
     */
    public function handle()
    {
        if (!$this->review->isValid()) {
            glsr_log()->error('Cannot toggle review status: Invalid Post Type.');
            return [];
        }
        if (!glsr()->can('edit_post', $this->postId)) {
            glsr_log()->error('Cannot toggle review status: Invalid permission.');
            return [];
        }
        $args = [
            'ID' => $this->postId,
            'post_status' => $this->status,
        ];
        $postId = wp_update_post($args, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message());
            return [];
        }
        return [
            'class' => 'status-'.$this->status,
            'counts' => $this->getStatusLinks(),
            'link' => $this->getPostLink($postId).$this->getPostState($postId),
            'pending' => wp_count_posts(glsr()->post_type, 'readable')->pending,
        ];
    }

    /**
     * @param int $postId
     * @return string
     */
    protected function getPostLink($postId)
    {
        $title = _draft_or_post_title($postId);
        return glsr(Builder::class)->a($title, [
            'aria-label' => '&#8220;'.esc_attr($title).'&#8221; ('._x('Edit', 'admin-text', 'site-reviews').')',
            'class' => 'row-title',
            'href' => get_edit_post_link($postId),
        ]);
    }

    /**
     * @param int $postId
     * @return string
     */
    protected function getPostState($postId)
    {
        return _post_states(get_post($postId), false);
    }

    /**
     * @return void|string
     */
    protected function getStatusLinks()
    {
        global $avail_post_stati, $wp_post_statuses;
        $avail_post_stati = get_available_post_statuses(glsr()->post_type);
        if (isset($wp_post_statuses['publish']->label_count)) {
            $wp_post_statuses['publish']->label_count = _nx_noop(
                'Approved <span class="count">(%s)</span>',
                'Approved <span class="count">(%s)</span>',
                'admin-text',
                'site-reviews'
            );
        }
        if (isset($wp_post_statuses['pending']->label_count)) {
            $wp_post_statuses['pending']->label_count = _nx_noop(
                'Unapproved <span class="count">(%s)</span>',
                'Unapproved <span class="count">(%s)</span>',
                'admin-text',
                'site-reviews'
            );
        }
        $hookName = 'edit-'.glsr()->post_type;
        set_current_screen($hookName);
        $table = new \WP_Posts_List_Table(['screen' => $hookName]);
        $views = apply_filters('views_'.$hookName, $table->get_views()); // get_views() is in the $compat_methods array for public access
        if (empty($views)) {
            return;
        }
        foreach ($views as $class => $view) {
            $views[$class] = sprintf('<li class="%s">%s', $class, $view);
        }
        return implode(" |</li>\t", $views).'</li>';
    }
}
