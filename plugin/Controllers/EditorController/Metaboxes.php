<?php

namespace GeminiLabs\SiteReviews\Controllers\EditorController;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;

class Metaboxes
{
    /**
     * @param int $postId
     * @return void
     */
    public function saveAssignedToMetabox($postId)
    {
        if (!wp_verify_nonce(Helper::filterInput('_nonce-assigned-to'), 'assigned_to')) {
            return;
        }
        $assignedTo = strval(Helper::filterInput('assigned_to'));
        glsr(Database::class)->update($postId, 'assigned_to', $assignedTo);
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function saveResponseMetabox($postId)
    {
        if (!wp_verify_nonce(Helper::filterInput('_nonce-response'), 'response')) {
            return;
        }
        $response = strval(Helper::filterInput('response'));
        glsr(Database::class)->update($postId, 'response', trim(wp_kses($response, [
            'a' => ['href' => [], 'title' => []],
            'em' => [],
            'strong' => [],
        ])));
    }
}
