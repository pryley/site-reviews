<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ReviewAssignedUsersTag extends ReviewTag
{
    protected function handle(): string
    {
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
    {
        $users = $this->review->assignedUsers();
        $names = [];
        foreach ($users as $user) {
            $names[] = glsr(Sanitizer::class)->sanitizeUserName($user);
        }
        if (empty($names)) {
            return '';
        }
        return sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($names));
    }
}
