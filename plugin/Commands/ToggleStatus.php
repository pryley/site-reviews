<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Posts_List_Table;

class ToggleStatus implements Contract
{
    public $id;
    public $status;

    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = 'approve' == $status
            ? 'publish'
            : 'pending';
    }

    /**
     * @return array
     */
    public function handle()
    {
        $postId = wp_update_post([
            'ID' => $this->id,
            'post_status' => $this->status,
        ]);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message());
            return [];
        }
        return [
            'class' => 'status-'.$this->status,
            'counts' => $this->getStatusLinks(),
            'link' => $this->getPostLink($postId).$this->getPostState($postId),
            'pending' => wp_count_posts(Application::POST_TYPE, 'readable')->pending,
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
        ob_start();
        _post_states(get_post($postId));
        return ob_get_clean();
    }

    /**
     * @return void|string
     */
    protected function getStatusLinks()
    {
        global $avail_post_stati;
        $hookName = 'edit-'.Application::POST_TYPE;
        set_current_screen($hookName);
        $avail_post_stati = get_available_post_statuses(Application::POST_TYPE);
        $table = new WP_Posts_List_Table(['screen' => $hookName]);
        // glsr_log($table);
        $views = apply_filters('views_'.$hookName, $table->get_views()); // uses compat get_views()
        if (empty($views)) {
            return;
        }
        foreach ($views as $class => $view) {
            $views[$class] = "\t<li class='$class'>$view";
        }
        return implode(' |</li>', $views).'</li>';
    }
}
