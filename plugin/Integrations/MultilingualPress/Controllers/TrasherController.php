<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Module\Trasher\Trasher;
use Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingUpdater;
use Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingView;
use function Inpsyde\MultilingualPress\resolve;

class TrasherController extends AbstractController
{
    /**
     * @action delete_post
     */
    public function syncDelete(int $postId): void
    {
        if (!str_starts_with((string) get_post_type($postId), glsr()->post_type)) {
            return;
        }
        static $syncingRelatedPosts;
        if ($syncingRelatedPosts) {
            return;
        }
        $syncingRelatedPosts = true;
        $copier = new ReviewCopier($postId, get_current_blog_id());
        $copier->run(static function ($context) {
            wp_delete_post($context->remotePostId(), true);
        });
        $syncingRelatedPosts = false;
    }

    /**
     * @action trashed_post
     * @action untrashed_post
     */
    public function syncTrash(int $postId): void
    {
        if (!str_starts_with((string) get_post_type($postId), glsr()->post_type)) {
            return;
        }
        static $syncingRelatedPosts;
        if ($syncingRelatedPosts) {
            return;
        }
        $syncingRelatedPosts = true;
        $copier = new ReviewCopier($postId, get_current_blog_id());
        $copier->run(static function ($context) {
            if ('trash' === $context->sourcePost()->post_status) {
                wp_trash_post($context->remotePostId());
            } else {
                wp_untrash_post($context->remotePostId());
            }
        });
        $syncingRelatedPosts = false;
    }

    /**
     * @action current_screen
     */
    public function removeDefaultTrasher(): void
    {
        if (!str_starts_with(glsr_current_screen()->post_type, glsr()->post_type)) {
            return;
        }
        glsr(Compatibility::class)->removeHook(
            'wp_trash_post',
            'trashRelatedPosts',
            Trasher::class
        );
        glsr(Compatibility::class)->removeHook(
            'save_post',
            'update',
            TrasherSettingUpdater::class
        );
        glsr(Compatibility::class)->removeHook(
            'post_submitbox_misc_actions',
            'render',
            TrasherSettingView::class
        );
    }
}
