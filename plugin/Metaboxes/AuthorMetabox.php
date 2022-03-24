<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxBuilder;
use GeminiLabs\SiteReviews\Review;

class AuthorMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        if (!Review::isReview($post) || !glsr()->can('edit_others_posts')) {
            return;
        }
        $id = glsr()->post_type.'-authordiv';
        $title = _x('Author', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'side');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        $placeholder = _x('Author Unknown', 'admin-text', 'site-reviews');
        $selected = $placeholder;
        $value = (empty($post->ID) ? get_current_user_id() : $post->post_author);
        if ($user = get_user_by('id', $value)) {
            $selected = $user->display_name;
        }
        echo glsr(MetaboxBuilder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'post_author_override',
            'text' => _x('Author', 'admin-text', 'site-reviews'),
        ]);
        echo glsr()->build('partials/listtable/filter', [
            'action' => 'filter-author',
            'class' => '',
            'id' => 'post_author_override',
            'name' => 'post_author_override',
            'options' => [0 => $placeholder],
            'placeholder' => $placeholder,
            'selected' => $selected,
            'value' => Cast::toInt($value),
        ]);
    }
}
