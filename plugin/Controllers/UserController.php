<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Role;

class UserController extends AbstractController
{
    /**
     * @param string[] $caps
     *
     * @return string[]
     *
     * @filter map_meta_cap
     */
    public function filterMapMetaCap(array $caps, string $cap, int $userId, array $args): array
    {
        if ('respond_to_'.glsr()->post_type !== $cap) {
            return $caps;
        }
        $review = glsr_get_review(Arr::get($args, 0));
        if (!$review->isValid()) {
            return ['do_not_allow'];
        }
        $caps = [];
        $respondToReviews = glsr(Role::class)->capability('respond_to_posts');
        if ($userId == $review->author_id) {
            $caps[] = $respondToReviews; // they are the author of the review
        }
        foreach ($review->assignedPosts() as $assignedPost) {
            if ($userId == $assignedPost->post_author) {
                $caps[] = $respondToReviews; // they are the author of the post that the review is assigned to
                break;
            }
        }
        if (!in_array($respondToReviews, $caps)) {
            $caps[] = glsr(Role::class)->capability('respond_to_others_posts');
        }
        return array_unique($caps);
    }

    /**
     * @param bool[]   $allcaps
     * @param string[] $caps
     *
     * @return bool[]
     *
     * @filter user_has_cap
     */
    public function filterUserHasCap(array $allcaps, array $caps, array $args): array
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
