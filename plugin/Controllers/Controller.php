<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Contracts\CommandContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use WP_Query;

abstract class Controller
{
    /**
     * @return void
     */
    public function download($filename, $content)
    {
        if (glsr()->can('edit_others_posts')) {
            nocache_headers();
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            echo html_entity_decode($content);
            exit;
        }
    }

    /**
     * @return mixed
     */
    public function execute(CommandContract $command)
    {
        return $command->handle();
    }

    /**
     * @return int
     */
    protected function getPostId()
    {
        return intval(filter_input(INPUT_GET, 'post'));
    }

    /**
     * @return bool
     */
    protected function hasQueryPermission(WP_Query $query)
    {
        global $pagenow;
        return glsr()->isAdmin()
            && $query->is_main_query()
            && glsr()->post_type === $query->get('post_type')
            && 'edit.php' === $pagenow;
    }

    /**
     * @return bool
     */
    protected function isNoticeAdminScreen()
    {
        return 'dashboard' === glsr_current_screen()->id || $this->isReviewAdminScreen();
    }

    /**
     * @return bool
     */
    protected function isReviewAdminPage()
    {
        return glsr()->isAdmin()
            && in_array(glsr()->post_type, [get_post_type(), filter_input(INPUT_GET, 'post_type')]);
    }

    /**
     * @return bool
     */
    protected function isReviewAdminScreen()
    {
        return Str::startsWith(glsr_current_screen()->post_type, glsr()->post_type);
    }

    /**
     * @return bool
     */
    protected function isReviewEditor()
    {
        $screen = glsr_current_screen();
        return ('post' == $screen->base)
            && glsr()->post_type == $screen->id
            && glsr()->post_type == $screen->post_type;
    }
}
