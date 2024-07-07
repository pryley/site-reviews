<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxBuilder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class AuthorMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        if (!glsr()->can('edit_others_posts')) {
            return;
        }
        $id = glsr()->post_type.'-authordiv';
        $title = _x('Author', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'side');
    }

    public function render(\WP_Post $post): void
    {
        $selected = esc_html_x('Author Unknown', 'admin-text', 'site-reviews');
        $value = $post->post_author;
        if (empty($post->ID)) {
            $value = get_current_user_id(); // This is an unsaved review draft
        }
        if ($user = get_user_by('id', $value)) {
            $selected = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
        }
        echo glsr(MetaboxBuilder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'post_author_override',
            'text' => esc_html_x('Author', 'admin-text', 'site-reviews'),
        ]);
        echo glsr()->build('partials/listtable/filter', [
            'action' => 'filter-author',
            'class' => '',
            'id' => 'post_author_override',
            'name' => 'post_author_override',
            'selected' => $selected,
            'value' => Cast::toInt($value),
        ]);
    }
}
