<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\ToggleStatusDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Request;

class ToggleStatus extends AbstractCommand
{
    public $postId;
    public $prevStatus;
    public $review;
    public $status;

    public function __construct(Request $request)
    {
        $args = glsr(ToggleStatusDefaults::class)->restrict($request->toArray());
        $review = glsr_get_review($args['post_id']);
        $this->postId = $args['post_id'];
        $this->prevStatus = $review->status;
        $this->review = $review;
        $this->status = $args['status'];
    }

    public function handle(): void
    {
        if (!$this->review->isValid()) {
            glsr_log()->error('Cannot toggle review status: Invalid Post Type.');
            $this->fail();
            return;
        }
        if (!glsr()->can('edit_post', $this->postId)) {
            glsr_log()->error('Cannot toggle review status: Invalid permission.');
            $this->fail();
            return;
        }
        $args = [
            'ID' => $this->postId,
            'post_status' => $this->status,
        ];
        $postId = wp_update_post($args, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message());
            $this->fail();
        }
    }

    public function response(): array
    {
        if (!$this->successful()) {
            return [];
        }
        return [
            'class' => "status-{$this->status}",
            'counts' => $this->getStatusLinks(),
            'link' => $this->getPostLink(),
            'pending' => wp_count_posts(glsr()->post_type, 'readable')->pending,
        ];
    }

    protected function getPostLink(): string
    {
        $title = _draft_or_post_title($this->postId);
        $link = glsr(Builder::class)->a($title, [
            'aria-label' => '&#8220;'.esc_attr($title).'&#8221; ('._x('Edit', 'admin-text', 'site-reviews').')',
            'class' => 'row-title',
            'href' => get_edit_post_link($this->postId),
        ]);
        return $link._post_states(get_post($this->postId), false);
    }

    protected function getStatusLinks(): string
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
        $views = apply_filters("views_{$hookName}", $table->get_views()); // get_views() is in the $compat_methods array for public access
        if (empty($views)) {
            return '';
        }
        foreach ($views as $class => $view) {
            $views[$class] = sprintf('<li class="%s">%s', $class, $view);
        }
        return implode(" |</li>\t", $views).'</li>';
    }
}
