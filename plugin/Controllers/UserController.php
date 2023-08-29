<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Role;

class UserController extends Controller
{
    /**
     * @param string[] $capabilities
     * @param string $capability
     * @param int $userId
     * @param array $args
     * @return string[]
     * @filter map_meta_cap
     */
    public function filterMapMetaCap($capabilities, $capability, $userId, $args)
    {
        if ('respond_to_'.glsr()->post_type !== $capability) {
            return $capabilities;
        }
        $review = glsr_get_review(Arr::get($args, 0));
        if (!$review->isValid()) {
            return ['do_not_allow'];
        }
        $capabilities = [];
        $respondToReviews = glsr(Role::class)->capability('respond_to_posts');
        if ($userId == $review->author_id) {
            $capabilities[] = $respondToReviews; // they are the author of the review
        }
        foreach ($review->assignedPosts() as $assignedPost) {
            if ($userId == $assignedPost->post_author) {
                $capabilities[] = $respondToReviews;  // they are the author of the post that the review is assigned to
                break;
            }
        }
        if (!in_array($respondToReviews, $capabilities)) {
            $capabilities[] = glsr(Role::class)->capability('respond_to_others_posts');
        }
        return array_unique($capabilities);
    }

    /**
     * @param bool[] $allcaps
     * @param string[] $caps
     * @param array $args
     * @return bool[]
     * @filter user_has_cap
     */
    public function filterUserHasCap($allcaps, $caps, $args)
    {
        $capability = Arr::get($args, 0);
        if (!in_array($capability, ['assign_post', 'unassign_post'])) {
            return $allcaps;
        }
        $postId = Arr::getAs('int', $args, 2);
        $status = get_post_status_object((string) get_post_status($postId));
        if (!$status) {
            return $allcaps;
        }
        $allcaps[$capability] = true;
        if (!$status->public && !$status->private) {
            unset($allcaps[$capability]);
        } elseif ('private' === $status->name && !current_user_can('read_post', $postId)) {
            unset($allcaps[$capability]);
        } elseif (post_password_required($postId) && !current_user_can('edit_post', $postId)) {
            unset($allcaps[$capability]);
        }
        return $allcaps;
    }
}
