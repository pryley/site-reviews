<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Contracts\CommandContract;

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
        if (method_exists($command, 'handle')) {
            return $command->handle();
        }
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
    protected function isReviewAdminPage()
    {
        return is_admin()
            && in_array(glsr()->post_type, [get_post_type(), filter_input(INPUT_GET, 'post_type')]);
    }
}
