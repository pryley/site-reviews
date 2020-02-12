<?php

namespace GeminiLabs\SiteReviews\Controllers;

use Exception;
use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Modules\Notice;
use InvalidArgumentException;
use WP_Error;

abstract class Controller
{
    /**
     * @return void
     */
    public function download($filename, $content)
    {
        if (!glsr()->can('edit_others_posts')) {
            return;
        }
        nocache_headers();
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo html_entity_decode($content);
        exit;
    }

    /**
     * @param object $command
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function execute($command)
    {
        $handlerClass = str_replace('Commands', 'Handlers', get_class($command));
        if (!class_exists($handlerClass)) {
            throw new InvalidArgumentException('Handler '.$handlerClass.' not found.');
        }
        try {
            return glsr($handlerClass)->handle($command);
        } catch (Exception $e) {
            status_header(400);
            glsr(Notice::class)->addError(new WP_Error('site_reviews_error', $e->getMessage()));
            glsr_log()->error($e->getMessage());
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
     * @param int $postId
     * @return bool
     */
    protected function isReviewPostId($postId)
    {
        return Application::POST_TYPE == get_post_field('post_type', $postId);
    }
}
