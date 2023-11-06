<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Contracts\CommandContract;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractController
{
    public function download($filename, $content): void
    {
        if (glsr()->can('edit_others_posts')) {
            nocache_headers();
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            echo html_entity_decode($content);
            exit;
        }
    }

    public function execute(CommandContract $command): CommandContract
    {
        $command->handle();
        return $command;
    }

    protected function getPostId(): int
    {
        return intval(filter_input(INPUT_GET, 'post'));
    }

    protected function hasQueryPermission(\WP_Query $query): bool
    {
        global $pagenow;
        return glsr()->isAdmin()
            && $query->is_main_query()
            && glsr()->post_type === $query->get('post_type')
            && 'edit.php' === $pagenow;
    }

    protected function isNoticeAdminScreen(): bool
    {
        return 'dashboard' === glsr_current_screen()->id || $this->isReviewAdminScreen();
    }

    protected function isReviewAdminPage(): bool
    {
        return glsr()->isAdmin() && in_array(glsr()->post_type, [
            filter_input(INPUT_GET, 'post_type'),
            get_post_type(),
        ]);
    }

    protected function isReviewAdminScreen(): bool
    {
        return str_starts_with(glsr_current_screen()->post_type, glsr()->post_type);
    }

    protected function isReviewEditor(): bool
    {
        $screen = glsr_current_screen();
        return ('post' === $screen->base)
            && glsr()->post_type === $screen->id
            && glsr()->post_type === $screen->post_type;
    }
}