<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Modules\Notice;

class TogglePinned implements Contract
{
    public $id;
    public $pinned;

    public function __construct($input)
    {
        $this->id = $input['id'];
        $this->pinned = isset($input['pinned'])
            ? wp_validate_boolean($input['pinned'])
            : null;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!get_post($this->id)) {
            return false;
        }
        if (!glsr()->can('edit_others_posts')) {
            $isPinned = (Database::class)->get($this->id, 'is_pinned');
            return wp_validate_boolean($isPinned);
        }
        if (is_null($this->pinned)) {
            $isPinned = glsr(Database::class)->get($this->id, 'is_pinned');
            $this->pinned = !wp_validate_boolean($isPinned);
        } else {
            $notice = $this->pinned
                ? _x('Review pinned.', 'admin-text', 'site-reviews')
                : _x('Review unpinned.', 'admin-text', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
        $result = glsr(RatingManager::class)->update($this->id, [
            'is_pinned' => $this->pinned,
        ]);
        return $this->pinned;
    }
}
