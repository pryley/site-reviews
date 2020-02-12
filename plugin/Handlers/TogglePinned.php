<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\TogglePinned as Command;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Notice;

class TogglePinned
{
    /**
     * @return bool
     */
    public function handle(Command $command)
    {
        if (!get_post($command->id)) {
            return false;
        }
        if (!glsr()->can('edit_others_posts')) {
            $meta = (Database::class)->get($command->id, 'pinned');
            return wp_validate_boolean($meta);
        }
        if (is_null($command->pinned)) {
            $meta = glsr(Database::class)->get($command->id, 'pinned');
            $command->pinned = !wp_validate_boolean($meta);
        } else {
            $notice = $command->pinned
                ? __('Review pinned.', 'site-reviews')
                : __('Review unpinned.', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
        glsr(Database::class)->update($command->id, 'pinned', $command->pinned);
        return $command->pinned;
    }
}
